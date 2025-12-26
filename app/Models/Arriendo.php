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
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'date',
        'fecha_entrega' => 'date',
        'fecha_devolucion_real' => 'date',
        'cerrado' => 'boolean',
        'total_alquiler' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    /* =======================
       RELACIONES
    ======================= */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class, 'obra_id');
    }



    /* =======================
       SCOPES ÚTILES PARA MÉTRICAS
    ======================= */

    // Solo dinero realmente cobrado
    public function scopePagados($query)
    {
        return $query->where('total_pagado', '>', 0);
    }

    // Solo con saldo pendiente
    public function scopeConSaldo($query)
    {
        return $query->where('saldo', '>', 0);
    }

   public function devoluciones()
{
    return $this->hasMany(\App\Models\DevolucionArriendo::class, 'arriendo_id');
}

public function incidencias()
{
    return $this->hasMany(\App\Models\Incidencia::class, 'arriendo_id');
}

public function items()
{
    return $this->hasMany(\App\Models\ArriendoItem::class, 'arriendo_id');
}


}
