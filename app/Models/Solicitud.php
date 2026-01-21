<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

   
    protected $fillable = [
        'solicitud_id',
        'cliente_id',
        'obra_id',
        'producto_id',
        'cantidad_solicitada',
        'cantidad_aprobada',
        'estado',
        'fecha_aprobado',
    ];

     public function arriendo()
    {
        return $this->belongsTo(Arriendo::class, 'solicitud_id');
    }

    /* =========================
       RELACIONES
    ========================== */

    // 👉 Cliente dueño de la solicitud
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // 👉 Obra asociada
    public function obra()
{
    return $this->belongsTo(Obra::class, 'obra_id');
}


    // 👉 Productos solicitados (tabla pivote)
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'solicitud_productos',
            'solicitud_id',
            'producto_id'
        )
        ->withPivot([
            'cantidad_solicitada',
            'cantidad_aprobada'
        ])
        ->withTimestamps();
    }
        public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

}
