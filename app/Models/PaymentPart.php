<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPart extends Model
{
    protected $fillable = ['payment_id','method','amount','reference'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
