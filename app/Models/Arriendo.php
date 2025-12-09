<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arriendo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'fecha_inicio',
        'fecha_fin',
        'precio',
        'estado',
    ];

    // Relación: un arriendo pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación: un arriendo pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
