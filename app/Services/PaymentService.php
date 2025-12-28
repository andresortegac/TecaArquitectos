<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentPart;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Crea un pago confirmado con partes.
     * $meta: info del pago (user_id, source_type, source_id, arriendo_id, client_id, etc.)
     * $parts: [['method'=>'efectivo','amount'=>1000,'reference'=>null], ...]
     */
    public function createConfirmedPayment(array $meta, array $parts): Payment
    {
        return DB::transaction(function () use ($meta, $parts) {
            $occurredAt = isset($meta['occurred_at'])
                ? Carbon::parse($meta['occurred_at'])
                : now();

            $businessDate = isset($meta['business_date'])
                ? Carbon::parse($meta['business_date'])->toDateString()
                : $occurredAt->toDateString();

            $total = 0;
            foreach ($parts as $p) {
                $amt = (int)($p['amount'] ?? 0);
                if ($amt > 0) $total += $amt;
            }

            if ($total <= 0) {
                throw new \InvalidArgumentException('El total del pago debe ser mayor a 0.');
            }

            $payment = Payment::create([
                'occurred_at'    => $occurredAt,
                'business_date'  => $businessDate,
                'total_amount'   => $total,
                'status'         => 'confirmed',
                'confirmed_at'   => now(),
                'confirmed_by'   => $meta['user_id'] ?? null,

                'source_type'    => $meta['source_type'] ?? null,
                'source_id'      => $meta['source_id'] ?? null,

                'client_id'      => $meta['client_id'] ?? null,
                'obra_id'        => $meta['obra_id'] ?? null,
                'arriendo_id'    => $meta['arriendo_id'] ?? null,

                'note'           => $meta['note'] ?? null,
            ]);

            foreach ($parts as $p) {
                $amt = (int)($p['amount'] ?? 0);
                if ($amt <= 0) continue;

                PaymentPart::create([
                    'payment_id' => $payment->id,
                    'method'     => $p['method'],
                    'amount'     => $amt,
                    'reference'  => $p['reference'] ?? null,
                ]);
            }

            return $payment->load('parts');
        });
    }
}
