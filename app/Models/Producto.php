<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
    public function getImagenUrlAttribute()
    {
        if (!$this->imagen) {
            return asset('img/product-icon.svg');
        }

        if (filter_var($this->imagen, FILTER_VALIDATE_URL)) {
            return $this->imagen;
        }

        $path = ltrim(str_replace('storage/', '', $this->imagen), '/');

        if (Storage::disk('public')->exists($path)) {
            return route('media.public', ['path' => $path]);
        }

        if (file_exists(public_path($path))) {
            return asset($path);
        }

        if (file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }

        return asset('img/product-icon.svg');
    }

}
 
