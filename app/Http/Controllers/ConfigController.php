<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;

class ConfigController extends Controller
{
    /**
     * Mostrar la pantalla de configuración
     */
    public function index()
    {
        // Siempre trabajamos con una sola configuración global
        $config = Configuracion::first();

        // Si no existe, la creamos (primer arranque del sistema)
        if (!$config) {
            $config = Configuracion::create([
                'stock_minimo' => 10,
                'alerta_stock' => true,
                'mes_defecto' => 'Enero',
                'bloquear_sin_stock' => true,
            ]);
        }

        return view('configuracion.index', [
            'config' => $config,
            'meses' => [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ]
        ]);
    }

    /**
     * Guardar configuración de Stock
     */
    public function stock(Request $request)
    {
        $request->validate([
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $config = Configuracion::first();

        $config->update([
            'stock_minimo' => $request->stock_minimo,
            'alerta_stock' => $request->has('alerta_stock'),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Configuración de stock actualizada');
    }

    /**
     * Guardar configuración de Reportes
     */
    public function reportes(Request $request)
    {
        $request->validate([
            'mes_defecto' => 'required|string',
        ]);

        $config = Configuracion::first();

        $config->update([
            'mes_defecto' => $request->mes_defecto,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Configuración de reportes actualizada');
    }

    /**
     * Guardar configuración de Inventario
     */
    public function inventario(Request $request)
    {
        $config = Configuracion::first();

        $config->update([
            'bloquear_sin_stock' => $request->has('bloquear_sin_stock'),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Configuración de inventario actualizada');
    }
}
