<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\ArriendoItem;
use App\Models\Cliente;
use App\Models\Incidencia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function clientesPendientes(Request $request)
    {
        $filters = $request->validate([
            'cliente' => 'nullable|string|max:120',
            'estado' => 'nullable|in:al_dia,moroso',
        ]);

        $query = ArriendoItem::query()
            ->with(['arriendo.cliente', 'arriendo.obra', 'producto'])
            ->where('saldo', '>', 0)
            ->orderByDesc('id');

        if (!empty($filters['cliente'])) {
            $query->whereHas('arriendo.cliente', function ($clienteQuery) use ($filters) {
                $clienteQuery->where('nombre', 'like', '%' . $filters['cliente'] . '%');
            });
        }

        $items = $query->get();

        $clientesMorosos = $items
            ->filter(fn ($item) => $item->arriendo?->cliente_id)
            ->groupBy(fn ($item) => $item->arriendo->cliente_id)
            ->map(function ($itemsCliente) {
                $primerItem = $itemsCliente->first();
                $cliente = $primerItem->arriendo?->cliente;

                $obras = $itemsCliente
                    ->map(function ($item) {
                        $obra = $item->arriendo?->obra;
                        if (!$obra) {
                            return null;
                        }

                        return $obra->direccion ?: $obra->detalle;
                    })
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $totalDeuda = (float) $itemsCliente->sum('saldo');
                $alquileres = $itemsCliente->pluck('arriendo_id')->filter()->unique()->count();
                $ultimoCobro = $itemsCliente->pluck('fecha_fin_item')->filter()->max();
                $productosAlquilados = $itemsCliente
                    ->map(fn ($item) => $item->producto?->nombre)
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $diasMora = $itemsCliente
                    ->map(function ($item) {
                        if (!$item->fecha_fin_item) {
                            return 0;
                        }

                        return Carbon::parse($item->fecha_fin_item)->diffInDays(now());
                    })
                    ->max();

                $estado = $diasMora > 0 ? 'moroso' : 'al_dia';
                $nivel = $diasMora >= 10 ? 'rojo' : ($diasMora > 0 ? 'amarillo' : 'verde');

                return (object) [
                    'nombre' => $cliente?->nombre ?? 'Cliente no disponible',
                    'obras' => $obras ?: '-',
                    'alquileres_pendientes' => $alquileres,
                    'productos_alquilados' => $productosAlquilados ?: '-',
                    'total_deuda' => $totalDeuda,
                    'ultimo_cobro' => $ultimoCobro ? Carbon::parse($ultimoCobro)->format('d/m/Y') : '-',
                    'dias_mora' => (int) $diasMora,
                    'estado' => $estado,
                    'nivel_mora' => $nivel,
                ];
            })
            ->values();

        if (!empty($filters['estado'])) {
            $clientesMorosos = $clientesMorosos
                ->where('estado', $filters['estado'])
                ->values();
        }

        $clientesMorosos = $clientesMorosos
            ->sortByDesc('total_deuda')
            ->values();

        $resumen = [
            'clientes' => $clientesMorosos->count(),
            'alquileres' => (int) $items->pluck('arriendo_id')->filter()->unique()->count(),
            'total_deuda' => (float) $items->sum('saldo'),
        ];

        $devolucionesQuery = ArriendoItem::query()
            ->with(['arriendo.cliente', 'arriendo.obra', 'producto'])
            ->withSum('devoluciones as cantidad_devuelta_total', 'cantidad_devuelta')
            ->orderByDesc('fecha_fin_item')
            ->orderByDesc('id');

        if (!empty($filters['cliente'])) {
            $devolucionesQuery->whereHas('arriendo.cliente', function ($clienteQuery) use ($filters) {
                $clienteQuery->where('nombre', 'like', '%' . $filters['cliente'] . '%');
            });
        }

        $reporteDevoluciones = $devolucionesQuery
            ->get()
            ->map(function ($item) {
                $cantidadAlquilada = (int) ($item->cantidad_inicial ?? 0);
                $cantidadDevuelta = min($cantidadAlquilada, (int) round($item->cantidad_devuelta_total ?? 0));
                $diferencia = max(0, $cantidadAlquilada - $cantidadDevuelta);

                if ($diferencia <= 0 || (int) ($item->cerrado ?? 0) === 1) {
                    $estado = 'Devuelto total';
                    $estadoClass = 'badge-ok';
                } elseif ($cantidadDevuelta > 0) {
                    $estado = 'Devuelto parcial';
                    $estadoClass = 'badge-warning';
                } else {
                    $estado = 'Pendiente';
                    $estadoClass = 'badge-risk';
                }

                $obra = $item->arriendo?->obra;
                $obraTexto = $obra?->direccion ?: ($obra?->detalle ?: '-');

                $fechaEstimada = $item->fecha_fin_item
                    ? Carbon::parse($item->fecha_fin_item)->format('d/m/Y')
                    : '-';

                return (object) [
                    'cliente' => $item->arriendo?->cliente?->nombre ?? 'Cliente no disponible',
                    'obra' => $obraTexto,
                    'herramienta' => $item->producto?->nombre ?? 'Herramienta no disponible',
                    'cantidad_alquilada' => $cantidadAlquilada,
                    'cantidad_devuelta' => $cantidadDevuelta,
                    'diferencia' => $diferencia,
                    'fecha_estimada_devolucion' => $fechaEstimada,
                    'estado' => $estado,
                    'estado_class' => $estadoClass,
                ];
            });

        return view('reportes.clientes-pendientes', [
            'clientesMorosos' => $clientesMorosos,
            'resumen' => $resumen,
            'reporteDevoluciones' => $reporteDevoluciones,
            'filters' => $filters,
        ]);
    }

    public function ingresosDiarios(Request $request)
    {
        $filters = $request->validate([
            'fecha' => 'nullable|date',
            'tipo_pago' => 'nullable|in:efectivo,transferencia,nequi,daviplata',
            'cliente' => 'nullable|string|max:120',
        ]);

        $fecha = $filters['fecha'] ?? now()->toDateString();

        $query = DB::table('payment_parts as pp')
            ->join('payments as p', 'p.id', '=', 'pp.payment_id')
            ->leftJoin('clientes as c', 'c.id', '=', 'p.client_id')
            ->leftJoin('obras as o', 'o.id', '=', 'p.obra_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.confirmed_by')
            ->leftJoin('devoluciones_arriendos as da', function ($join) {
                $join->on('da.id', '=', 'p.source_id')
                    ->where('p.source_type', '=', 'App\\Models\\DevolucionArriendo');
            })
            ->where('p.status', 'confirmed')
            ->whereDate('p.business_date', $fecha);

        if (!empty($filters['tipo_pago'])) {
            $query->where('pp.method', $filters['tipo_pago']);
        }

        if (!empty($filters['cliente'])) {
            $query->where('c.nombre', 'like', '%' . $filters['cliente'] . '%');
        }

        $registros = (clone $query)
            ->select([
                'p.id as payment_id',
                'p.business_date',
                'p.occurred_at',
                'c.nombre as cliente',
                'o.direccion as obra_direccion',
                'o.detalle as obra_detalle',
                'pp.method as tipo_pago',
                'pp.amount as valor_recibido',
                'u.name as usuario',
                'p.note',
                'p.source_type',
                'da.total_cobrado as devol_total_cobrado',
                'da.pago_recibido as devol_pago_recibido',
            ])
            ->orderByDesc('p.occurred_at')
            ->orderByDesc('pp.id')
            ->paginate(25)
            ->withQueryString();

        $resumen = [
            'fecha' => Carbon::parse($fecha)->format('d/m/Y'),
            'ingresos_totales' => (clone $query)->sum('pp.amount'),
            'pagos_registrados' => (clone $query)->distinct('p.id')->count('p.id'),
            'abonos_parciales' => (clone $query)
                ->whereRaw('COALESCE(da.pago_recibido, 0) < COALESCE(da.total_cobrado, 0)')
                ->distinct('p.id')
                ->count('p.id'),
        ];

        return view('reportes.ingresos-diarios', [
            'registros' => $registros,
            'resumen' => $resumen,
            'filters' => [
                'fecha' => $fecha,
                'tipo_pago' => $filters['tipo_pago'] ?? '',
                'cliente' => $filters['cliente'] ?? '',
            ],
        ]);
    }

    public function clienteDetallado(Request $request)
    {
        $data = $this->buildClienteDetalladoData($request);

        return view('reportes.cliente-detallado', $data);
    }

    public function clienteDetalladoPdf(Request $request)
    {
        $data = $this->buildClienteDetalladoData($request);

        $pdf = Pdf::loadView('reportes.cliente-detallado-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-detallado-cliente-' . now()->format('Ymd_His') . '.pdf');
    }

    public function incidenciasDiasNoCobrados(Request $request)
    {
        $filters = $request->validate([
            'cliente' => 'nullable|string|max:120',
            'tipo' => 'nullable|string|max:80',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        ]);

        $fechaDesde = $filters['fecha_desde'] ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $filters['fecha_hasta'] ?? now()->toDateString();

        $query = Incidencia::query()
            ->with(['arriendo.cliente', 'arriendo.obra'])
            ->where(function ($q) {
                $q->where('dias', '>', 0)
                    ->orWhere('tipo', 'LLUVIA');
            })
            ->whereDate('created_at', '>=', $fechaDesde)
            ->whereDate('created_at', '<=', $fechaHasta)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (!empty($filters['cliente'])) {
            $query->whereHas('arriendo.cliente', function ($clienteQuery) use ($filters) {
                $clienteQuery->where('nombre', 'like', '%' . $filters['cliente'] . '%');
            });
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', 'like', '%' . $filters['tipo'] . '%');
        }

        $registros = $query->paginate(25)->withQueryString();

        $resumen = [
            'total_incidencias' => (clone $query)->count(),
            'dias_descontados' => (clone $query)->sum('dias'),
            'clientes_afectados' => (clone $query)->get()
                ->pluck('arriendo.cliente_id')
                ->filter()
                ->unique()
                ->count(),
        ];

        return view('reportes.incidencias-no-cobrados', [
            'registros' => $registros,
            'resumen' => $resumen,
            'filters' => [
                'cliente' => $filters['cliente'] ?? '',
                'tipo' => $filters['tipo'] ?? '',
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ],
        ]);
    }

    public function perdidasMantenimiento(Request $request)
    {
        $filters = $request->validate([
            'cliente' => 'nullable|string|max:120',
            'evento' => 'nullable|in:daño,perdida,mantenimiento',
            'estado_cobro' => 'nullable|in:cobrado,pendiente',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        ]);

        $fechaDesde = $filters['fecha_desde'] ?? now()->startOfMonth()->toDateString();
        $fechaHasta = $filters['fecha_hasta'] ?? now()->toDateString();

        $query = Incidencia::query()
            ->with(['arriendo.cliente', 'arriendo.items.producto', 'arriendo.producto'])
            ->whereDate('created_at', '>=', $fechaDesde)
            ->whereDate('created_at', '<=', $fechaHasta)
            ->where('costo', '>', 0)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (!empty($filters['cliente'])) {
            $query->whereHas('arriendo.cliente', function ($clienteQuery) use ($filters) {
                $clienteQuery->where('nombre', 'like', '%' . $filters['cliente'] . '%');
            });
        }

        $incidencias = $query->get()->map(function ($incidencia) {
            $tipoRaw = strtoupper((string) ($incidencia->tipo ?? ''));
            $descripcion = strtolower((string) ($incidencia->descripcion ?? ''));

            if ($tipoRaw === 'DANO' || str_contains($descripcion, 'daño') || str_contains($descripcion, 'dano')) {
                $evento = 'daño';
            } elseif ($tipoRaw === 'MANTENIMIENTO' || str_contains($descripcion, 'mantenimiento')) {
                $evento = 'mantenimiento';
            } elseif ($tipoRaw === 'PERDIDA' || str_contains($descripcion, 'perdida') || str_contains($descripcion, 'pérdida')) {
                $evento = 'perdida';
            } else {
                $evento = 'daño';
            }

            $arriendo = $incidencia->arriendo;
            $herramienta = '-';

            if ($arriendo?->items && $arriendo->items->isNotEmpty()) {
                $herramientas = $arriendo->items
                    ->map(fn ($item) => $item->producto?->nombre)
                    ->filter()
                    ->unique()
                    ->values();
                $herramienta = $herramientas->isNotEmpty() ? $herramientas->implode(', ') : '-';
            } elseif ($arriendo?->producto) {
                $herramienta = $arriendo->producto->nombre;
            }

            $estadoCobro = ((float) ($arriendo->saldo ?? 0) > 0) ? 'pendiente' : 'cobrado';

            return (object) [
                'herramienta' => $herramienta,
                'cliente' => $arriendo?->cliente?->nombre ?? 'Cliente no disponible',
                'evento' => $evento,
                'costo' => (float) ($incidencia->costo ?? 0),
                'fecha' => optional($incidencia->created_at)?->format('d/m/Y H:i') ?? '-',
                'estado_cobro' => $estadoCobro,
            ];
        });

        if (!empty($filters['evento'])) {
            $incidencias = $incidencias->where('evento', $filters['evento'])->values();
        }

        if (!empty($filters['estado_cobro'])) {
            $incidencias = $incidencias->where('estado_cobro', $filters['estado_cobro'])->values();
        }

        $resumen = [
            'registros' => $incidencias->count(),
            'costo_total' => (float) $incidencias->sum('costo'),
            'pendientes_cobro' => $incidencias->where('estado_cobro', 'pendiente')->count(),
        ];

        return view('reportes.perdidas-mantenimiento', [
            'registros' => $incidencias,
            'resumen' => $resumen,
            'filters' => [
                'cliente' => $filters['cliente'] ?? '',
                'evento' => $filters['evento'] ?? '',
                'estado_cobro' => $filters['estado_cobro'] ?? '',
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ],
        ]);
    }

    private function buildClienteDetalladoData(Request $request): array
    {
        $today = now();
        $defaultFrom = $today->copy()->startOfMonth()->toDateString();
        $defaultTo = $today->toDateString();

        $filters = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        ]);

        $fechaDesde = $filters['fecha_desde'] ?? $defaultFrom;
        $fechaHasta = $filters['fecha_hasta'] ?? $defaultTo;

        $query = Arriendo::query()
            ->with([
                'cliente',
                'obra',
                'items.producto',
                'items.devoluciones',
                'transportes',
                'incidencias',
            ])
            ->whereDate('fecha_inicio', '>=', $fechaDesde)
            ->whereDate('fecha_inicio', '<=', $fechaHasta)
            ->orderByDesc('fecha_inicio')
            ->orderByDesc('id');

        if (!empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }

        $arriendos = $query->get();

        $filas = $arriendos->flatMap(function ($arriendo) {
            return $arriendo->items->map(function ($item) use ($arriendo) {
                $devoluciones = $item->devoluciones ?? collect();

                $diasCobrados = (int) $devoluciones->sum('dias_cobrables');
                $diasNoCobrados = (int) $devoluciones->sum(function ($d) {
                    return (int) ($d->domingos_desc ?? 0) + (int) ($d->dias_lluvia_desc ?? 0);
                });

                if ($diasCobrados === 0 && !empty($item->fecha_inicio_item)) {
                    $inicio = Carbon::parse($item->fecha_inicio_item)->startOfDay();
                    $fin = !empty($item->fecha_fin_item)
                        ? Carbon::parse($item->fecha_fin_item)->startOfDay()
                        : now()->startOfDay();
                    $diasCobrados = max(1, $inicio->diffInDays($fin));
                }

                $descuentoAplicado = (float) $devoluciones->sum(function ($d) {
                    $diasDesc = (int) ($d->domingos_desc ?? 0) + (int) ($d->dias_lluvia_desc ?? 0);
                    return $diasDesc * (float) ($d->tarifa_diaria ?? 0) * (int) ($d->cantidad_devuelta ?? 0);
                });

                $costoPerdidas = (float) $devoluciones->sum('total_merma');
                $costoTransporte = (float) $devoluciones->sum('costo_transporte');

                return (object) [
                    'cliente' => $arriendo->cliente?->nombre ?? 'Cliente no disponible',
                    'cliente_documento' => $arriendo->cliente?->documento ?? '-',
                    'cliente_telefono' => $arriendo->cliente?->telefono ?? '-',
                    'cliente_email' => $arriendo->cliente?->email ?? '-',
                    'obra' => $arriendo->obra?->direccion ?: ($arriendo->obra?->detalle ?: '-'),
                    'herramienta' => $item->producto?->nombre ?? 'Herramienta no disponible',
                    'fecha_alquiler' => !empty($item->fecha_inicio_item)
                        ? Carbon::parse($item->fecha_inicio_item)->format('d/m/Y')
                        : (!empty($arriendo->fecha_inicio) ? Carbon::parse($arriendo->fecha_inicio)->format('d/m/Y') : '-'),
                    'fecha_devolucion' => !empty($item->fecha_fin_item)
                        ? Carbon::parse($item->fecha_fin_item)->format('d/m/Y')
                        : '-',
                    'dias_cobrados' => $diasCobrados,
                    'dias_no_cobrados' => $diasNoCobrados,
                    'costo_alquiler' => (float) ($item->total_alquiler ?? 0),
                    'costo_transporte' => $costoTransporte,
                    'descuentos_aplicados' => $descuentoAplicado,
                    'costos_perdidas_mantenimiento' => $costoPerdidas,
                    'pagos_abonos' => (float) ($item->total_pagado ?? 0),
                    'saldo_final' => (float) ($item->saldo ?? 0),
                ];
            });
        })->values();

        $clienteSeleccionado = !empty($filters['cliente_id'])
            ? Cliente::find($filters['cliente_id'])
            : null;

        $obrasAsociadas = $filas->pluck('obra')->filter()->unique()->values();

        $resumen = [
            'registros' => $filas->count(),
            'total_alquiler' => (float) $filas->sum('costo_alquiler'),
            'total_transporte' => (float) $filas->sum('costo_transporte'),
            'total_descuentos' => (float) $filas->sum('descuentos_aplicados'),
            'total_perdidas' => (float) $filas->sum('costos_perdidas_mantenimiento'),
            'total_pagado' => (float) $filas->sum('pagos_abonos'),
            'saldo_final' => (float) $filas->sum('saldo_final'),
        ];

        return [
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre']),
            'clienteSeleccionado' => $clienteSeleccionado,
            'obrasAsociadas' => $obrasAsociadas,
            'filas' => $filas,
            'resumen' => $resumen,
            'filters' => [
                'cliente_id' => $filters['cliente_id'] ?? '',
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
            ],
        ];
    }
}
