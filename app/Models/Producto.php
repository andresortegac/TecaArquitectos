<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public const IMAGE_DIR = 'uploads/productos';
    

    protected $fillable = [
        'nombre',
        'categorias',
        'cantidad',
        'costo',
        'ubicacion',
        'estado',
        'imagen',
        'tarifa_diaria',
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
    public static function imageUrl(?string $imagen): string
    {
        if (empty($imagen)) {
            return asset('img/product-icon.svg');
        }

        $imagen = ltrim($imagen, '/');

        if (str_starts_with($imagen, 'http://') || str_starts_with($imagen, 'https://')) {
            return $imagen;
        }

        if (str_starts_with($imagen, self::IMAGE_DIR . '/') || str_starts_with($imagen, 'storage/')) {
            return asset($imagen);
        }

        return asset('storage/' . $imagen);
    }

    public function getImagenUrlAttribute()
    {
        return self::imageUrl($this->imagen);
    }

}
 
