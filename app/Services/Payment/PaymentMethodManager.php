<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Events\PaymentCreated;
use App\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class PaymentMethodManager
{
    public function __construct(
        private PaymentMethodRepository $paymentMethodRepository,
        private Container $container,
        private PaymentMethod $mPaymentMethod,
        private Dispatcher $eventDispatcher,
    )
    {
    }
    
    /**
     * @return Collection
     */
    public function getAllEnabled(): Collection
    {
        $paymentMethods = $this->paymentMethodRepository->getList();
        $paymentMethods->map(function (PaymentMethod $item) {
            $item->selected = false;
        });
        
        return $paymentMethods;
    }
    
    /**
     * @param int $id
     * @return PaymentMethodInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPaymentService(int $id): PaymentMethodInterface
    {
        $paymentMethod = $this->mPaymentMethod->findOrFail($id);
        if (! $paymentMethod instanceof PaymentMethod) {
            throw (new ModelNotFoundException)->setModel(
                get_class($this->mPaymentMethod), $id
            );
        }
        
        return $this->container->get($paymentMethod->class_name);
    }
    
    public function pay(int $methodId, array $request): PaymentResponse
    {
        $paymentService = $this->getPaymentService($methodId);
        $paymentResponse = $paymentService->pay($request);
        
        $paymentResponse->setPaymentId($request['paymentId']);
        $this->eventDispatcher->dispatch(new PaymentCreated($paymentResponse));
        
        return $paymentResponse;
    }
    
    public function enable(int $id): void
    {
        $this->paymentMethodRepository->changeStatus($id, true);
    }
    
    public function disable(int $id): void
    {
        $this->paymentMethodRepository->changeStatus($id, false);
    }
    
    public function addToDB(array $methodConfig): void
    {
        $this->paymentMethodRepository->addFromConfig($methodConfig);
    }
    
    public function removeFromDB(int $id): void
    {
        $this->paymentMethodRepository->delete($id);
    }
    
    public function changePriority(int $id, int $newVal): void
    {
        $this->paymentMethodRepository->updateById($id, ['priority' => $newVal]);
    }
}
