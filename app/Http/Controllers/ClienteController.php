<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:120',
        ]);

        $query = Cliente::query()->latest();

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('documento', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('telefono', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['q'] . '%');
            });
        }

        $clientes = $query->paginate(10)->withQueryString();

        return view('clientes.index', compact('clientes', 'filters'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'documento' => 'nullable|string|max:100',
        ]);

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado correctamente');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('obras');
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'documento' => 'nullable|string|max:100',
        ]);

        $cliente->update($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado');
    }

    /**
     * ✅ NUEVO: Devuelve las obras del cliente para el select "Obra" en arriendos/create
     * Ruta: GET /clientes/{cliente}/obras
     * Respuesta: JSON [{id, nombre}, ...]
     */
    public function obras(Cliente $cliente)
    {
        return response()->json(
            $cliente->obras()
                ->select('id', 'direccion')
                ->get()
        );
    }
}
 
