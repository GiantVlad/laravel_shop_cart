<?php

namespace App\Http\Controllers;

use App\Enums\AdminPaymentMethodStatuses;
use App\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use App\Services\Payment\PaymentMethodManager;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPaymentMethodsController extends Controller
{
    public function __construct(
        private ConfigContract $config,
        private PaymentMethodRepository $paymentMethodRepository,
        private PaymentMethodManager $paymentMethodManager,
    ) {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function list(): View
    {
        $paymentMethodsFromCfg = $this->config->get('payments.methods') ?? [];
        $keys = array_keys($paymentMethodsFromCfg);
        $paymentMethodsFromDB = $this->paymentMethodRepository->getList(false);
        $result = [];
        /** @var PaymentMethod $method */
        foreach ($paymentMethodsFromDB as $method) {
            $status = AdminPaymentMethodStatuses::MISSED_IN_CFG;
            if (in_array($method->config_key, $keys)) {
                $status = $method->enabled ? AdminPaymentMethodStatuses::ENABLED : AdminPaymentMethodStatuses::DISABLED;
                $keys = array_diff($keys, [$method->config_key]);
            }
            $result[] = [
                'id' => $method->id,
                'label' => $method->label,
                'status' => $status,
                'configKey' => $method->config_key,
                'priority' => $method->priority,
                'className' => $method->class_name,
            ];
        }
        
        foreach ($keys as $cfgKey) {
            $result[] = [
                'id' => 0,
                'label' => $paymentMethodsFromCfg[$cfgKey]['label'],
                'status' => AdminPaymentMethodStatuses::MISSED_IN_DB,
                'configKey' => $cfgKey,
                'priority' => 0,
                'className' =>$paymentMethodsFromCfg[$cfgKey]['class_name'],
            ];
        }
        
        return view('admin.payment-methods', ['paymentMethods' => $result]);
    }
    
    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function action(int $id, Request $request): JsonResponse
    {
        $action = $request->get('action');
        switch ($action) {
            case 'disable':
                $this->paymentMethodManager->disable($id);
                break;
            case 'add_to_db':
                $key = $request->get('payment_key');
                $paymentMethodsFromCfg = $this->config->get('payments.methods') ?? [];
                $methodConfig = $paymentMethodsFromCfg[$key] ?? null;
                $methodConfig['config_key'] = $key;
                $this->paymentMethodManager->addToDB($methodConfig);
                break;
            case 'remove_from_db':
                $this->paymentMethodManager->removeFromDB($id);
                break;
            default:
                $this->paymentMethodManager->enable($id);
        }
        
        return response()->json(['updated']);
    }
    
    public function priority(int $id, Request $request): JsonResponse
    {
        $newVal = \abs((int)$request->get('val'));
       
        $this->paymentMethodManager->changePriority($id, $newVal);
        
        return response()->json(['updated']);
    }
}
