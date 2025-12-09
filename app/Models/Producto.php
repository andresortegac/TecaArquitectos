<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public function arriendos()
    {
        return $this->hasMany(Arriendo::class);
    }

}
