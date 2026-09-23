<?php

use App\PaymentMethod;
use App\ShippingMethod;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Add the "Pickup from the store" shipping method. Store pickup accepts every
     * payment method, cash included.
     */
    public function up(): void
    {
        $shippingMethod = ShippingMethod::updateOrCreate(
            ['class_name' => 'PickupShippingMethod'],
            ['priority' => 2, 'enable' => true]
        );

        $shippingMethod->paymentMethods()->sync(PaymentMethod::pluck('id')->all());
    }

    public function down(): void
    {
        ShippingMethod::where('class_name', 'PickupShippingMethod')->delete();
    }
};
