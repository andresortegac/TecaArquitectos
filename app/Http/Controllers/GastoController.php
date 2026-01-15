<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function create()
    {
        return view('gastos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'monto'       => 'required|numeric|min:0',
            'fecha'       => 'required|date',
        ]);

        Gasto::create($data);

        return redirect()
            ->route('gastos.index')
            ->with('success', 'Gasto registrado correctamente');
    }

    public function index()
    {
        $gastos = Gasto::orderBy('fecha', 'desc')->get();
        return view('gastos.index', compact('gastos'));
    }
}
