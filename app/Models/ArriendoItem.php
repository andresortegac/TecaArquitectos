<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArriendoItem extends Model
{
    protected $table = 'arriendo_items';

    protected $fillable = [
        'arriendo_id','producto_id',
        'cantidad_inicial','cantidad_actual',
        'fecha_inicio_item','fecha_fin_item','tarifa_diaria',
        'cerrado','estado',
        'precio_total','total_alquiler','total_merma','total_pagado','saldo',
    ];

    protected $casts = [
        'fecha_inicio_item' => 'datetime',
        'fecha_fin_item' => 'datetime',
        'tarifa_diaria' => 'float',

        'cantidad_inicial' => 'integer',
        'cantidad_actual' => 'integer',
        'cerrado' => 'boolean',
        'precio_total' => 'float',
        'total_alquiler' => 'float',
        'total_merma' => 'float',
        'total_pagado' => 'float',
        'saldo' => 'float',
    ];

    public function arriendo()
    {
        return $this->belongsTo(\App\Models\Arriendo::class, 'arriendo_id');
    }

    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id');
    }

    // ✅ Historial de devoluciones / abonos por herramienta (ITEM)
    public function devoluciones()
    {
        return $this->hasMany(\App\Models\DevolucionArriendo::class, 'arriendo_item_id')
            ->orderBy('id', 'desc');
    }
}
