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
      public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:120',
            'categoria' => 'nullable|string|max:120',
        ]);

        $query = Producto::query()->orderBy('id');

        if (!empty($filters['q'])) {
            $query->where('nombre', 'like', '%' . $filters['q'] . '%');
        }

        if (!empty($filters['categoria'])) {
            $query->where('categorias', $filters['categoria']);
        }

        $productos = $query->paginate(20)->withQueryString();

        $categorias = Producto::select('categorias')
            ->distinct()
            ->whereNotNull('categorias')
            ->pluck('categorias');

        $resumen = [
            'total_productos' => Producto::count(),
            'total_stock' => (int) Producto::sum('cantidad'),
        ];

        return view('productos.index', compact('productos', 'categorias', 'resumen', 'filters'));
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
        $filters = request()->validate([
            'q' => 'nullable|string|max:120',
            'estado' => 'nullable|in:sin_stock,stock_bajo',
        ]);

        $config = Configuracion::first();

        $stockMinimo = $config?->stock_minimo ?? 10;

        $query = Producto::query()
            ->where('cantidad', '<=', $stockMinimo)
            ->orderBy('cantidad', 'asc');

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('categorias', 'like', '%' . $filters['q'] . '%');
            });
        }

        if (($filters['estado'] ?? '') === 'sin_stock') {
            $query->where('cantidad', 0);
        } elseif (($filters['estado'] ?? '') === 'stock_bajo') {
            $query->where('cantidad', '>', 0);
        }

        $productos = $query->get();

        $resumen = [
            'total_alertas' => $productos->count(),
            'sin_stock' => $productos->where('cantidad', 0)->count(),
            'stock_bajo' => $productos->where('cantidad', '>', 0)->count(),
        ];

        return view('productos.alertas', compact(
            'productos',
            'stockMinimo',
            'resumen',
            'filters'
        ));
    }
     
}
