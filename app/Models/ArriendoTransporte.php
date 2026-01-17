<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArriendoTransporte extends Model
{
    protected $table = 'arriendo_transportes';

    protected $fillable = [
        'arriendo_id', 'tipo', 'fecha', 'valor', 'nota'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'valor' => 'decimal:2',
    ];

    public function arriendo()
    {
        return $this->belongsTo(Arriendo::class);
    }
}
