<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionArriendo extends Model
{
    protected $table = 'devoluciones_arriendo'; // ✅ TU TABLA REAL

    protected $fillable = [
        'arriendo_id',
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
}
