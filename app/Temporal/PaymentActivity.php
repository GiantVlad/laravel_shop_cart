<?php

namespace App\Temporal;

use App\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\Http;
use Temporal\Activity\ActivityMethod;

class PaymentActivity implements PaymentActivityInterface
{
    public function __construct(private PaymentRepository $paymentRepository)
    {
    }
    
    #[ActivityMethod(name: "paymentStatus")]
    public function checkStatus(int $paymentId): bool
    {
        $response = Http::get('http://localhost/stub/payment');
        if ($response->ok()) {
            $this->paymentRepository->updateById($paymentId, ['status' => Payment::STATUS_CONFIRMED]);
            
            return true;
        }
    
        return false;
    }
}
