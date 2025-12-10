<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::latest()->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'cantidad'  => 'required|integer|min:0',
            'costo'     => 'required|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'estado'    => 'required|in:disponible,dañado,reservado',
        ]);

        Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto agregado a bodega correctamente');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'cantidad'  => 'required|integer|min:0',
            'costo'     => 'required|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'estado'    => 'required|in:disponible,dañado,reservado',
        ]);

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado de bodega');
    }
}
