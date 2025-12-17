<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre_cliente',
        'telefono_cliente',
        'fecha_solicitud',
        'estado',
    ];

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'solicitud_productos'
        )->withPivot('cantidad_solicitada', 'cantidad_aprobada')
         ->withTimestamps();
    }
}
