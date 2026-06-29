<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyClosure extends Model
{
    protected $fillable = [
        'business_date',
        'closed_at',
        'total_amount',
        'total_gastos',
        'utilidad',
        'method_breakdown',
        'closed_by',
        'observacion',
    ];

    protected $casts = [
        'business_date' => 'date',
        'closed_at' => 'datetime',
        'method_breakdown' => 'array',
    ];

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'daily_closure_payments');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
