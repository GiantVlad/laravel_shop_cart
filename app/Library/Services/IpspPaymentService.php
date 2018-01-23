<?php
/**
 * Created by PhpStorm.
 * User: ale
 * Date: 23.01.2018
 * Time: 17:52
 */

namespace App\Library\Services;


class SelfPaymentService implements PaymentServiceInterface
{
    public function getName () : string
    {
        return 'Hello';
    }
}