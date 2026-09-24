<?php

use App\PaymentMethod;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Register the "Cash" payment method (pay on pickup).
     */
    public function up(): void
    {
        PaymentMethod::updateOrCreate(
            ['config_key' => 'cash'],
            [
                'label' => 'Cash',
                'class_name' => 'App\\Services\\Payment\\CashPayment',
                'priority' => 3,
                'enabled' => true,
            ]
        );
    }

    public function down(): void
    {
        PaymentMethod::where('config_key', 'cash')->delete();
    }
};
