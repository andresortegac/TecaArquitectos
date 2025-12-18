<?php

namespace App\Imports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductosImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        

        return new Producto([
            'nombre'    => $row['nombre'] ?? null,
            'categorias' => $row['categorias'] ?? null,
            'cantidad'  => $row['cantidad'] ?? 0,
            'costo'     => $row['unitario'] ?? 0,
            'ubicacion' => $row['ubicacion'] ?? null,
            'estado'    => $row['estado'] ?? 'disponible',
        ]);
    }
}
