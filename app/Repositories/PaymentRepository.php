<?php

namespace App\Repositories;

use App\Payment;

class PaymentRepository
{
    public function __construct(
        private Payment $mPayment
    )
    {}
    
    public function updateById(int $id, array $fieldsValues): void
    {
        /** @var Payment $payment */
        $payment = $this->mPayment->findOrFail($id);
        $payment->update($fieldsValues);
    }
}
