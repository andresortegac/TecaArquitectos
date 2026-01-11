<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Obra;
use Illuminate\Http\Request;

class ObraController extends Controller
{
    public function create(Cliente $cliente)
    {
        return view('obras.create', compact('cliente'));
    }

    public function store(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'direccion' => 'required|string|max:255',
            'detalle' => 'nullable|string',
        ]);

        $cliente->obras()->create($data);

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Obra creada correctamente');
    }

    public function edit(Cliente $cliente, Obra $obra)
    {
        return view('obras.edit', compact('cliente', 'obra'));
    }

    public function update(Request $request, Cliente $cliente, Obra $obra)
    {
        $data = $request->validate([
            'direccion' => 'required|string|max:255',
            'detalle' => 'nullable|string',
        ]);

        $obra->update($data);

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Obra actualizada');
    }

    public function destroy(Cliente $cliente, Obra $obra)
    {
        $obra->delete();

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('success', 'Obra eliminada');
    }

}
