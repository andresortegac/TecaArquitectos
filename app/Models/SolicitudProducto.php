<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudProducto extends Model
{
    protected $fillable = [
        'solicitud_id',
        'producto_id',
        'cantidad_solicitada',
        'cantidad_aprobada'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
