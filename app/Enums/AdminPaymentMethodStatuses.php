<?php

namespace App\Enums;

class AdminPaymentMethodStatuses
{
    public const DISABLED = 0;
    public const ENABLED = 1;
    public const MISSED_IN_CFG = 2;
    public const MISSED_IN_DB = 3;
    
    public const STATUSES = [
        0 => 'Disabled',
        1 => 'Enabled',
        2 => 'Missing from the config',
        3 => 'Missing from the DB'
    ];
}
