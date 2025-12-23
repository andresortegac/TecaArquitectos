<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    

    protected $fillable = [
        'nombre',
        'categorias',
        'cantidad',
        'costo',
        'ubicacion',
        'estado',
        'tarifa_diaria'
    ];

    // 👇 Lo que ya tenías (intocable)
    public function arriendos()
    {
        return $this->hasMany(Arriendo::class);
    }

    // 🆕 NUEVO: relación con solicitudes
    public function solicitudes()
    {
        return $this->belongsToMany(
            Solicitud::class,
            'solicitud_productos'
        )->withPivot(
            'cantidad_solicitada',
            'cantidad_aprobada'
        )->withTimestamps();
    }
        public function getImagenUrlAttribute()
    {
        return asset($this->imagen);
    }

}
 