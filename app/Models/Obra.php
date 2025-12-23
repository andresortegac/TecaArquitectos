<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $fillable = [
        'cliente_id',
        'direccion',
        'detalle',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
