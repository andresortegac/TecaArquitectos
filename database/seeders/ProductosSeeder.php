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
                'nombre'     => 'Andamio metálico VERDE - 1,50x1,50m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 50,
                'costo'      => 3500,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Andamio metálico CAFÉ - 1,50x1,50m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 50,
                'costo'      => 3500,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Ángulo metálico 1,20m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 100,
                'costo'      => 3000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Ángulo metálico 0,60m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 100,
                'costo'      => 2000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Chapetas',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 200,
                'costo'      => 800,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Cercha metálicas 2m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 30,
                'costo'      => 7000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Cercha metálicas 3m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 30,
                'costo'      => 10000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Puntal 2m (Ext. 3,80m) VERDE',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 60,
                'costo'      => 9000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Puntal 2m (Ext. 3,80m) ROJO',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 48,
                'costo'      => 2500,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Puntal GRIS (para subalquiler)',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 24,
                'costo'      => 2000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.20 m x 1.20 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 18,
                'costo'      => 4300,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.20 m x 1.20 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 18,
                'costo'      => 4300,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
         
            [
                'nombre'     => 'Formaleta - Tablero 0.25 m x 1.20 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 1,
                'costo'      => 10000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.25 m x 0.60 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 2,
                'costo'      => 250000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.30 m x 1.20 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 8,
                'costo'      => 1200,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.30 m x 0.60 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 24,
                'costo'      => 1200,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.40 m x 1.20 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 48,
                'costo'      => 2500,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaleta - Tablero 0.40 m x 0.60 m',
                'categorias' => 'Encofrado',
                'cantidad'   => 24,
                'costo'      => 2000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Plataforma metálica de seguridad',
                'categorias' => 'Accesorio de Seguridad',
                'cantidad'   => 18,
                'costo'      => 4300,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Tensores',
                'categorias' => 'Herramientas y Equipos',
                'cantidad'   => 3,
                'costo'      => 2000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Apisonador tipo canguro - Gasolina',
                'categorias' => 'Herramientas y Equipos',
                'cantidad'   => 1,
                'costo'      => 10000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Pluma grúa eléctrica 220V - 300 kg',
                'categorias' => 'Herramientas y Equipos',
                'cantidad'   => 2,
                'costo'      => 250000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Columnas tipo L de 0,25m x 3m',
                'categorias' => 'Estructura y Soportes',
                'cantidad'   => 8,
                'costo'      => 1200,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Juego de ruedas para andamios',
                'categorias' => 'Accesorio de Seguridad',
                'cantidad'   => 3,
                'costo'      => 5000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Escalera metálica tipo tijera (8 pasos)',
                'categorias' => 'Accesorio de Seguridad',
                'cantidad'   => 1,
                'costo'      => 3000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Tablón de madera',
                'categorias' => 'Accesorio de Seguridad',
                'cantidad'   => 12,
                'costo'      => 3000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Tijera metálica corta',
                'categorias' => 'Herramientas y Equipos',
                'cantidad'   => 25,
                'costo'      => 2000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Tijera metálica larga',
                'categorias' => 'Herramientas y Equipos',
                'cantidad'   => 25,
                'costo'      => 3000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Extensión eléctrica 110V de 10 m',
                'categorias' => 'Electricidad',
                'cantidad'   => 1,
                'costo'      => 9000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Extensión eléctrica 110V de 20 m',
                'categorias' => 'Electricidad',
                'cantidad'   => 1,
                'costo'      => 10000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Extensión eléctrica 110V de 50 m',
                'categorias' => 'Electricidad',
                'cantidad'   => 1,
                'costo'      => 15000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Extensión eléctrica 110V de 100 m',
                'categorias' => 'Electricidad',
                'cantidad'   => 1,
                'costo'      => 20000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaletas-Tableros-chapetas-Angulos 20 A 3m',
                'categorias' => 'Expecial',
                'cantidad'   => 100,
                'costo'      => 22000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaletas-Tableros-chapetas-Angulos 25 A 3m',
                'categorias' => 'Expecial',
                'cantidad'   => 1,
                'costo'      => 25000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaletas-Tableros-chapetas-Angulos 30 A 3m',
                'categorias' => 'Expecial',
                'cantidad'   => 1,
                'costo'      => 30000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],
            [
                'nombre'     => 'Formaletas-Tableros-chapetas-Angulos 40 A 3m',
                'categorias' => 'Expecial',
                'cantidad'   => 1,
                'costo'      => 40000,
                'ubicacion'  => 'B/ Jardin',
                'estado'     => 'disponible',
                
            ],

                    ];

                    foreach ($productos as $producto) {
                        Producto::create($producto);
                    }
                }
}
