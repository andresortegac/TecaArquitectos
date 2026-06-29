<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyClosure extends Model
{
    protected $fillable = [
        'month_start',
        'month_end',
        'closed_at',
        'total_amount',
        'total_gastos',
        'utilidad',
        'method_breakdown',
        'closed_by',
        'observacion',
    ];

    protected $casts = [
        'month_start' => 'date',
        'month_end' => 'date',
        'closed_at' => 'datetime',
        'method_breakdown' => 'array',
    ];

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'monthly_closure_payments');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
