<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    // Nombre de la tabla (por claridad explícita)
    protected $table = 'configuraciones';

    // Permitimos asignación masiva SOLO de estos campos
    protected $fillable = [
        'stock_minimo',
        'alerta_stock',
        'mes_defecto',
        'bloquear_sin_stock',
    ];

    // Casts automáticos para no pelear con los tipos
    protected $casts = [
        'alerta_stock' => 'boolean',
        'bloquear_sin_stock' => 'boolean',
        'stock_minimo' => 'integer',
    ];
}
