<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\Producto;

class Arriendo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'cantidad',
        'fecha_inicio',
        'fecha_fin',
        'precio_total',
        'estado',
        'fecha_entrega',
        'fecha_devolucion_real',
        'obra_id',
        'cerrado',
        'dias_transcurridos',
        'domingos_desc',
        'dias_lluvia_desc',
        'dias_cobrables',
        'total_alquiler',
        'total_merma',
        'total_pagado',
        'saldo',
        'dias_mora',
        'semaforo_pago',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_entrega' => 'date',
        'fecha_devolucion_real' => 'date',
        'cerrado' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
