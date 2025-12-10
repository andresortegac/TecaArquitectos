<?php

namespace App\Http\Controllers;

use App\Models\Arriendo;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;

class ArriendoController extends Controller
{
    public function index()
    {
        $arriendos = Arriendo::with(['cliente','producto'])
            ->latest()
            ->paginate(10);

        return view('arriendos.index', compact('arriendos'));
    }

    public function create()
    {
        $clientes  = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.create', compact('clientes','productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'producto_id'  => 'required|exists:productos,id',
            'cantidad'     => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'precio_total' => 'required|numeric|min:0',
            'estado'       => 'required|in:activo,devuelto,vencido',
        ]);

        Arriendo::create($data);

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo creado correctamente');
    }

    public function edit(Arriendo $arriendo)
    {
        $clientes  = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('arriendos.edit', compact('arriendo','clientes','productos'));
    }

    public function update(Request $request, Arriendo $arriendo)
    {
        $data = $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'producto_id'  => 'required|exists:productos,id',
            'cantidad'     => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'precio_total' => 'required|numeric|min:0',
            'estado'       => 'required|in:activo,devuelto,vencido',
        ]);

        $arriendo->update($data);

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo actualizado correctamente');
    }

    public function destroy(Arriendo $arriendo)
    {
        $arriendo->delete();

        return redirect()->route('arriendos.index')
            ->with('success', 'Arriendo eliminado');
    }
}
