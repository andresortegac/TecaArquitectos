<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false; // porque tu tabla solo tiene created_at (no updated_at)

    protected $casts = [
        'costo' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function arriendo()
    {
        return $this->belongsTo(Arriendo::class);
    }
}
