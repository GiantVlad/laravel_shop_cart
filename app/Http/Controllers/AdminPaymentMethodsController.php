<?php

namespace App\Http\Controllers;

use App\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminPaymentMethodsController extends Controller
{
    public function __construct(
        private ConfigContract $config,
        private PaymentMethodRepository $paymentMethodRepository
    ) {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function list(): View
    {
        $paymentMethodsFromCfg = $this->config->get('payments')['methods'] ?? [];
        $keys = array_keys($paymentMethodsFromCfg);
        $paymentMethodsFromDB = $this->paymentMethodRepository->getList(false);
        $result = [];
        /** @var PaymentMethod $method */
        foreach ($paymentMethodsFromDB as $method) {
            $status = 'missing from the config';
            if (in_array($method->config_key, $keys)) {
                $status = $method->enabled ? 'Enabled' : 'Disabled';
                $keys = array_diff($keys, [$method->config_key]);
            }
            $result[] = [
                'label' => $method->label,
                'status' => $status,
                'configKey' => $method->config_key,
                'priority' => $method->priority,
                'className' => $method->class_name,
            ];
        }
        
        foreach ($keys as $cfgKey) {
            $result[] = [
                'label' => $paymentMethodsFromCfg['label'],
                'status' => 'missing from the DB',
                'configKey' => $cfgKey,
                'priority' => 0,
                'className' =>$paymentMethodsFromCfg['class_name'],
            ];
        }
        
        return view('admin.payment-methods', ['paymentMethods' => $result]);
    }
    
    /**
     * @param Request $request
     */
    public function delete(Request $request): void
    {
    }
}
