<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyClosure extends Model
{
    protected $fillable = ['business_date','closed_at','total_amount','method_breakdown','closed_by'];

    protected $casts = [
        'business_date' => 'date',
        'closed_at' => 'datetime',
        'method_breakdown' => 'array',
    ];

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'daily_closure_payments');
    }
}
