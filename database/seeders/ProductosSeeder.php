<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre'     => 'Cemento Gris 50kg',
                'categorias' => 'Materiales',
                'cantidad'   => 120,
                'costo'      => 32000,
                'ubicacion'  => 'Bodega A - Estante 1',
                'estado'     => 'disponible',
                'imagen'     => null,
            ],
            [
                'nombre'     => 'Arena Lavada',
                'categorias' => 'Agregados',
                'cantidad'   => 80,
                'costo'      => 45000,
                'ubicacion'  => 'Bodega A - Patio',
                'estado'     => 'disponible',
                'imagen'     => null,
            ],
            [
                'nombre'     => 'Taladro Industrial',
                'categorias' => 'Herramientas',
                'cantidad'   => 15,
                'costo'      => 180000,
                'ubicacion'  => 'Bodega B - Estante 3',
                'estado'     => 'disponible',
                'imagen'     => null,
            ],
            [
                'nombre'     => 'Andamio Metálico',
                'categorias' => 'Estructuras',
                'cantidad'   => 25,
                'costo'      => 95000,
                'ubicacion'  => 'Bodega C',
                'estado'     => 'disponible',
                'imagen'     => null,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
