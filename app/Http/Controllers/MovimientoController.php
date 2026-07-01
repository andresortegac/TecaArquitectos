<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Producto;
use App\Exports\MovimientosExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MovimientoController extends Controller
{
    public function create()
    {
        $productos = Producto::all();
        $movimientos = Movimiento::with('producto')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('movimientos.create', compact('productos', 'movimientos'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required',
            'fecha' => 'required|date',
            'tipo' => 'required|in:ingreso,salida,fuera_servicio,producto_alquilado,producto_devuelto',
            'cantidad' => 'required|integer|min:1',
        ]);

        $producto = Producto::findOrFail($request->producto_id);

        // Lógica de stock (aquí está la magia 🪄)
        switch ($request->tipo) {
            case 'ingreso':
            case 'ajuste_positivo':
            case 'producto_devuelto':
                $producto->cantidad += $request->cantidad;
                break;

            case 'salida':
            case 'fuera_servicio':
            case 'producto_alquilado':
                $producto->cantidad -= $request->cantidad;
                break;
        }

        $producto->save();

        Movimiento::create($request->all());

        return redirect()->back()->with('success', 'Movimiento registrado correctamente');
    }
        public function export()
    {
        return Excel::download(new MovimientosExport, 'movimientos.xlsx');
    }
}
 
