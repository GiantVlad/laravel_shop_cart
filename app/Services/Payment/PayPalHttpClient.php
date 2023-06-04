<?php

declare(strict_types=1);

namespace App\Services\Payment;

use BraintreeHttp\HttpClient;
use BraintreeHttp\Injector;
use PayPal\Core\AuthorizationInjector;
use PayPal\Core\GzipInjector;
use PayPal\Core\PayPalEnvironment;
use PayPal\Core\UserAgent;

class PayPalHttpClient extends HttpClient
{
    private ?string $refreshToken;
    public Injector $authInjector;

    public function __construct(PayPalEnvironment $environment, ?string $refreshToken = NULL)
    {
        parent::__construct($environment);
        $this->refreshToken = $refreshToken;
        $this->authInjector = new AuthorizationInjector($this, $environment, $this->refreshToken);
        $this->addInjector($this->authInjector);
        $this->addInjector(new GzipInjector());
    }

    public function userAgent()
    {
        return UserAgent::getValue();
    }
}
