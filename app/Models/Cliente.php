<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Obra;
use App\Models\Arriendo;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'documento',
    ];

    public function arriendos()
    {
        return $this->hasMany(Arriendo::class);
    }

    public function obras()
    {
        return $this->hasMany(Obra::class);
    }

}
