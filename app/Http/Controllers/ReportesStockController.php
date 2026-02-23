<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Arriendo;
use App\Models\ArriendoItem;
use App\Models\Movimiento;
use App\Models\Payment;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ReportesStockController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function movimientos(Request $request)
    {
        $filters = $request->validate([
            'tipo' => 'nullable|in:ingreso,salida,ajuste_positivo,ajuste_negativo',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
            'producto' => 'nullable|string|max:120',
        ]);

        $query = Movimiento::query()
            ->with('producto')
            ->orderByDesc('fecha')
            ->orderByDesc('id');

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['fecha_desde'])) {
            $query->whereDate('fecha', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->whereDate('fecha', '<=', $filters['fecha_hasta']);
        }

        if (!empty($filters['producto'])) {
            $query->whereHas('producto', function ($productoQuery) use ($filters) {
                $productoQuery->where('nombre', 'like', '%' . $filters['producto'] . '%');
            });
        }

        $movimientos = (clone $query)
            ->paginate(20)
            ->withQueryString();

        $resumen = [
            'total_registros' => (clone $query)->count(),
            'total_unidades' => (clone $query)->sum('cantidad'),
            'entradas' => (clone $query)
                ->whereIn('tipo', ['ingreso', 'ajuste_positivo'])
                ->sum('cantidad'),
            'salidas' => (clone $query)
                ->whereIn('tipo', ['salida', 'ajuste_negativo'])
                ->sum('cantidad'),
        ];

        return view('reportes.movimientos', [
            'movimientos' => $movimientos,
            'resumen' => $resumen,
            'filters' => $filters,
        ]);
    }

    public function entradasSalidas(Request $request)
    {
        $query = Movimiento::with('producto')
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $movimientos = $query->get();

        return view('reportes.entradas_salidas', compact('movimientos'));
    }

    public function reportes()
    {
        $hoy = Carbon::now();
        $inicioMesActual = $hoy->copy()->startOfMonth();
        $inicioMesAnterior = $hoy->copy()->subMonthNoOverflow()->startOfMonth();
        $finMesAnterior = $hoy->copy()->subMonthNoOverflow()->endOfMonth();

        $totalVentas = Venta::count();
        $totalAlquileres = Arriendo::count();
        $ingresosVentas = (float) Venta::sum('total');
        $ingresosAlquileres = (float) ArriendoItem::sum('total_alquiler');
        $ingresosTotales = $ingresosVentas + $ingresosAlquileres;

        $costoEstimadoVendido = (float) Movimiento::query()
            ->with('producto:id,costo')
            ->where('tipo', 'salida')
            ->get()
            ->sum(function ($movimiento) {
                return (float) ($movimiento->cantidad ?? 0) * (float) ($movimiento->producto->costo ?? 0);
            });

        $gananciaEstimada = $ingresosTotales - $costoEstimadoVendido;
        $productosVendidos = (int) Movimiento::where('tipo', 'salida')->sum('cantidad');
        $productosAlquilados = (int) ArriendoItem::sum('cantidad_inicial');

        $rolAdminExiste = Role::where('name', 'admin')->where('guard_name', 'web')->exists();
        $rolVendedorExiste = Role::where('name', 'vendedor')->where('guard_name', 'web')->exists();

        $usuariosActivosAdmin = $rolAdminExiste ? User::role('admin')->count() : 0;
        $usuariosActivosVendedor = $rolVendedorExiste ? User::role('vendedor')->count() : 0;
        $usuariosActivos = $usuariosActivosAdmin + $usuariosActivosVendedor;

        $abonosPendientes = (int) ArriendoItem::where('saldo', '>', 0)->count();
        $saldoPorCobrar = (float) ArriendoItem::where('saldo', '>', 0)->sum('saldo');
        $multasGeneradas = (float) ArriendoItem::sum('total_merma');

        $topRentable = ArriendoItem::query()
            ->select('producto_id', DB::raw('SUM(total_alquiler + total_merma) as total_rentable'))
            ->whereNotNull('producto_id')
            ->groupBy('producto_id')
            ->orderByDesc('total_rentable')
            ->with('producto:id,nombre')
            ->first();

        $productosRentables = $topRentable && $topRentable->producto
            ? $topRentable->producto->nombre . ' ($' . number_format((float) $topRentable->total_rentable, 0) . ')'
            : '-';

        $productosDisponibles = Producto::where('cantidad', '>', 0)->count();
        $productosAlquiladosActualmente = (int) ArriendoItem::where('cerrado', 0)->sum('cantidad_actual');
        $productosBajoStock = Producto::where('cantidad', '>', 0)->where('cantidad', '<=', 5)->count();
        $productosFueraInventario = Producto::where('cantidad', 0)->count();

        $clientesRegistrados = Cliente::count();
        $clientesConDeudas = ArriendoItem::where('saldo', '>', 0)
            ->whereHas('arriendo', fn ($q) => $q->whereNotNull('cliente_id'))
            ->get()
            ->pluck('arriendo.cliente_id')
            ->filter()
            ->unique()
            ->count();
        $clientesFrecuentes = Cliente::has('arriendos', '>=', 3)->count();
        $nuevosClientesMes = Cliente::whereBetween('created_at', [$inicioMesActual, $hoy])->count();

        $productoMasVendidoRow = Movimiento::query()
            ->select('producto_id', DB::raw('SUM(cantidad) as total'))
            ->where('tipo', 'salida')
            ->whereNotNull('producto_id')
            ->groupBy('producto_id')
            ->orderByDesc('total')
            ->with('producto:id,nombre')
            ->first();
        $productoMasVendido = $productoMasVendidoRow && $productoMasVendidoRow->producto
            ? $productoMasVendidoRow->producto->nombre . ' (' . (int) $productoMasVendidoRow->total . ')'
            : '-';

        $productoMasAlquiladoRow = ArriendoItem::query()
            ->select('producto_id', DB::raw('SUM(cantidad_inicial) as total'))
            ->whereNotNull('producto_id')
            ->groupBy('producto_id')
            ->orderByDesc('total')
            ->with('producto:id,nombre')
            ->first();
        $productoMasAlquilado = $productoMasAlquiladoRow && $productoMasAlquiladoRow->producto
            ? $productoMasAlquiladoRow->producto->nombre . ' (' . (int) $productoMasAlquiladoRow->total . ')'
            : '-';

        $ingresosPorMes = [];
        foreach (Venta::select('fecha', 'total')->get() as $venta) {
            if (!$venta->fecha) {
                continue;
            }
            $ym = Carbon::parse($venta->fecha)->format('Y-m');
            $ingresosPorMes[$ym] = ($ingresosPorMes[$ym] ?? 0) + (float) $venta->total;
        }
        foreach (ArriendoItem::select('fecha_inicio_item', 'total_alquiler')->whereNotNull('fecha_inicio_item')->get() as $item) {
            $ym = Carbon::parse($item->fecha_inicio_item)->format('Y-m');
            $ingresosPorMes[$ym] = ($ingresosPorMes[$ym] ?? 0) + (float) $item->total_alquiler;
        }
        arsort($ingresosPorMes);
        $mesMayoresIngresos = !empty($ingresosPorMes)
            ? array_key_first($ingresosPorMes)
            : null;
        $mesMayoresIngresosTexto = $mesMayoresIngresos
            ? Carbon::createFromFormat('Y-m', $mesMayoresIngresos)->translatedFormat('F Y')
            : '-';

        $usuarioMasVentasRow = Payment::query()
            ->select('confirmed_by', DB::raw('COUNT(*) as total'))
            ->where('status', 'confirmed')
            ->whereNotNull('confirmed_by')
            ->groupBy('confirmed_by')
            ->orderByDesc('total')
            ->first();
        $usuarioMasVentas = '-';
        if ($usuarioMasVentasRow) {
            $usuario = User::find($usuarioMasVentasRow->confirmed_by);
            if ($usuario) {
                $usuarioMasVentas = $usuario->name . ' (' . (int) $usuarioMasVentasRow->total . ')';
            }
        }

        $ingresosMesActual = (float) Venta::whereBetween('fecha', [$inicioMesActual->toDateString(), $hoy->toDateString()])->sum('total')
            + (float) ArriendoItem::whereBetween('fecha_inicio_item', [$inicioMesActual, $hoy])->sum('total_alquiler');
        $ingresosMesAnterior = (float) Venta::whereBetween('fecha', [$inicioMesAnterior->toDateString(), $finMesAnterior->toDateString()])->sum('total')
            + (float) ArriendoItem::whereBetween('fecha_inicio_item', [$inicioMesAnterior, $finMesAnterior])->sum('total_alquiler');

        $crecimientoMensual = 0.0;
        if ($ingresosMesAnterior > 0) {
            $crecimientoMensual = (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100;
        } elseif ($ingresosMesActual > 0) {
            $crecimientoMensual = 100.0;
        }

        $metricas = [
            'resumen_general' => [
                'total_ventas' => $totalVentas,
                'total_alquileres' => $totalAlquileres,
                'ingresos_totales' => $ingresosTotales,
                'ganancia_estimada' => $gananciaEstimada,
                'productos_vendidos' => $productosVendidos,
                'productos_alquilados' => $productosAlquilados,
                'clientes_registrados' => $clientesRegistrados,
                'usuarios_activos' => $usuariosActivos,
                'usuarios_activos_detalle' => "Admin: {$usuariosActivosAdmin} / Vendedor: {$usuariosActivosVendedor}",
            ],
            'resumen_financiero' => [
                'ingresos_ventas' => $ingresosVentas,
                'ingresos_alquileres' => $ingresosAlquileres,
                'abonos_pendientes' => $abonosPendientes,
                'saldo_por_cobrar' => $saldoPorCobrar,
                'multas_generadas' => $multasGeneradas,
                'productos_mas_rentables' => $productosRentables,
            ],
            'inventario' => [
                'total_productos' => Producto::count(),
                'productos_disponibles' => $productosDisponibles,
                'productos_alquilados_actualmente' => $productosAlquiladosActualmente,
                'productos_bajo_stock' => $productosBajoStock,
                'productos_fuera_inventario' => $productosFueraInventario,
            ],
            'clientes' => [
                'total_clientes' => $clientesRegistrados,
                'clientes_con_deudas' => $clientesConDeudas,
                'clientes_frecuentes' => $clientesFrecuentes,
                'nuevos_clientes_mes' => $nuevosClientesMes,
            ],
            'kpis' => [
                'producto_mas_vendido' => $productoMasVendido,
                'producto_mas_alquilado' => $productoMasAlquilado,
                'mes_mayores_ingresos' => $mesMayoresIngresosTexto,
                'usuario_mas_ventas' => $usuarioMasVentas,
                'crecimiento_mensual' => $crecimientoMensual,
            ],
        ];

        return view('reportes.generalrep', [
            'metricas' => $metricas,
        ]);
    }

    public function productosAlquiladosDetallado(Request $request)
    {
        $filters = $request->validate([
            'buscar' => 'nullable|string|max:120',
        ]);

        $query = ArriendoItem::query()
            ->with(['producto', 'arriendo.cliente', 'arriendo.obra'])
            ->where('cantidad_actual', '>', 0)
            ->orderByDesc('fecha_inicio_item')
            ->orderByDesc('id');

        if (!empty($filters['buscar'])) {
            $buscar = $filters['buscar'];
            $query->where(function ($q) use ($buscar) {
                $q->whereHas('producto', function ($productoQ) use ($buscar) {
                    $productoQ->where('nombre', 'like', '%' . $buscar . '%');
                })->orWhereHas('arriendo.cliente', function ($clienteQ) use ($buscar) {
                    $clienteQ->where('nombre', 'like', '%' . $buscar . '%');
                })->orWhereHas('arriendo.obra', function ($obraQ) use ($buscar) {
                    $obraQ->where('direccion', 'like', '%' . $buscar . '%')
                        ->orWhere('detalle', 'like', '%' . $buscar . '%');
                });
            });
        }

        $registros = $query->paginate(20)->withQueryString();

        return view('reportes.productos-alquilados-detallado', [
            'registros' => $registros,
            'filters' => $filters,
        ]);
    }
}
