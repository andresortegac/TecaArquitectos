<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Imports\ProductosImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Configuracion;

class ProductoController extends Controller
{
        public function index()
    {
        $productos = Producto::all();

        $categorias = Producto::select('categorias')
            ->distinct()
            ->whereNotNull('categorias')
            ->pluck('categorias');

        return view('productos.index', compact('productos', 'categorias'));
    }


        public function create()
    {
        return view('productos.create');
    }

        public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'categorias' => 'nullable|string|max:255',
            'cantidad'  => 'required|integer|min:0',
            'costo'     => 'required|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'estado'    => 'required|in:disponible,dañado,reservado',
            'imagen'    => 'nullable|image|max:5120',
        ]);

        // 👉 Guardar imagen
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')
                ->store('productos', 'public');
        }

        Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto agregado correctamente');
    }


        public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

        public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255',
            'categorias' => 'nullable|string|max:255',
            'cantidad'  => 'required|integer|min:0',
            'costo'     => 'required|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'estado'    => 'required|in:disponible,dañado,reservado',
            'imagen' => 'nullable|image|max:5120',

        ]);

        // 📸 ACTUALIZAR IMAGEN
        if ($request->hasFile('imagen')) {

            // borrar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $data['imagen'] = $request->file('imagen')
                ->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

        public function destroy(Producto $producto)
    {
        // 🗑️ borrar imagen del storage
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado de bodega');
    }
        public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,csv'
        ]);

        Excel::import(new ProductosImport, $request->file('archivo'));

        return redirect()->route('productos.index')
            ->with('success', 'Productos importados correctamente');
    }
        /* =========================
       ALERTAS DE STOCK
    ========================== */
    public function alertasStock()
    {
        $config = Configuracion::first();

        $stockMinimo = $config?->stock_minimo ?? 10;

        $productos = Producto::where('cantidad', '<=', $stockMinimo)
            ->orderBy('cantidad', 'asc')
            ->get();

        return view('productos.alertas', compact(
            'productos',
            'stockMinimo'
        ));
    }
     
}
