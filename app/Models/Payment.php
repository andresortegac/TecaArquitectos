<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'occurred_at','business_date','total_amount','status',
        'confirmed_at','confirmed_by',
        'cancelled_at','cancelled_by','cancel_reason',
        'source_type','source_id',
        'client_id','obra_id','arriendo_id',
        'note',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'business_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function parts()
    {
        return $this->hasMany(PaymentPart::class);
    }
}
