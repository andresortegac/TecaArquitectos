<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionArriendo extends Model
{
    protected $table = 'devoluciones_arriendos';
    public $timestamps = true;

    protected $fillable = [
        'arriendo_id',
        'arriendo_item_id',
        'fecha_devolucion',
        'cantidad_devuelta',
        'dias_transcurridos',
        'domingos_desc',
        'dias_lluvia_desc',
        'dias_cobrables',
        'tarifa_diaria',
        'total_alquiler',
        'total_merma',
        'total_cobrado',
        'pago_recibido',
        'cantidad_restante',
        'saldo_resultante',
        'descripcion_incidencia',
        'nota',
        'saldo_devolucion',

        // ✅ transporte
        'transporte_herramientas',
        'detalle_transporte',
        'costo_transporte',
    ];

    protected $casts = [
        'fecha_devolucion' => 'date',
        'cantidad_devuelta' => 'integer',
        'dias_transcurridos' => 'integer',
        'domingos_desc' => 'integer',
        'dias_lluvia_desc' => 'integer',
        'dias_cobrables' => 'integer',

        'tarifa_diaria' => 'float',
        'total_alquiler' => 'float',
        'total_merma' => 'float',
        'costo_transporte' => 'float',
        'total_cobrado' => 'float',
        'pago_recibido' => 'float',
        'saldo_resultante' => 'float',
        'saldo_devolucion' => 'float',
        'cantidad_restante' => 'integer',
    ];

    public function arriendo()
    {
        return $this->belongsTo(\App\Models\Arriendo::class, 'arriendo_id');
    }

    public function arriendoItem()
    {
        return $this->belongsTo(\App\Models\ArriendoItem::class, 'arriendo_item_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('latest', function ($query) {
            $query->orderByDesc('id');
        });
    }
}
