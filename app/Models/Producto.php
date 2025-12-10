<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria',
        'cantidad',
        'costo',
        'ubicacion',
        'estado',
    ];

    public function arriendos()
    {
        return $this->hasMany(Arriendo::class);
    }
}
 