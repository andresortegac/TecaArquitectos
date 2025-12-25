<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionArriendo extends Model
{
    // ✅ Tabla real en tu BD
    protected $table = 'devoluciones_arriendos';

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
    ];

    public function arriendo()
    {
        return $this->belongsTo(\App\Models\Arriendo::class, 'arriendo_id');
    }

    // ✅ NUEVO: relación con el ITEM (herramienta) para ver el historial por producto
    public function arriendoItem()
    {
        return $this->belongsTo(\App\Models\ArriendoItem::class, 'arriendo_item_id');
    }
}
