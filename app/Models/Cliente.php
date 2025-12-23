<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre','telefono','email','documento','direccion'
    ];

    public function arriendos()
    {
        return $this->hasMany(Arriendo::class);
    }

    // ✅ NUEVO: relación con obras (para cargar obras según cliente)
    public function obras()
    {
        return $this->hasMany(\App\Models\Obra::class);
    }
}
