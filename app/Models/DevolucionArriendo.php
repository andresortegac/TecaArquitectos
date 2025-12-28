<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionArriendo extends Model
{
    // ✅ Tabla real en tu BD
    protected $table = 'devoluciones_arriendos';

    // ✅ (Recomendado) Laravel detecta created_at/updated_at por defecto.
    // Si tu tabla los tiene (tú dijiste que sí), déjalo así.
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
        'total_cobrado' => 'float',
        'pago_recibido' => 'float',
        'saldo_resultante' => 'float',
        'cantidad_restante' => 'integer',
    ];

    public function arriendo()
    {
        return $this->belongsTo(\App\Models\Arriendo::class, 'arriendo_id');
    }

    // ✅ Relación con ITEM (puede ser null en devoluciones del PADRE)
    public function arriendoItem()
    {
        return $this->belongsTo(\App\Models\ArriendoItem::class, 'arriendo_item_id');
    }

    // ✅ (Opcional útil) ordenar por defecto por el más reciente
    protected static function booted()
    {
        static::addGlobalScope('latest', function ($query) {
            $query->orderByDesc('id');
        });
    }
}
