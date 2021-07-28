<?php

namespace App\Library\Services\Ipsp;
use App\Library\Services\PaymentResponseInterface;

/**
 * Class Response
 */
class Response implements PaymentResponseInterface
{
    private array $data;
    protected $response_status = '';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     * @return null
     */
    public function __get(string $name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : NULL;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function isSuccess()
    {
        return $this->response_status == 'success';
    }

    public function isFailure()
    {
        return $this->response_status == 'failure';
    }

    public function redirectTo($prop = '')
    {
        if ($this->$prop) {
            header(sprintf('Location: %s', $this->$prop));
        }
    }

    public function redirectToCheckout()
    {
        $this->redirectTo('checkout_url');
    }

    public function isCaptured()
    {
        $data = $this->getCapturedTransAction();
	
        if (!array_key_exists('capture_status', $data)) {
            throw new \Exception('invalid response');
        }
        return $data['capture_status'] != 'captured' ? false : true;
    }

    private function getCapturedTransAction()
    {
        foreach ($this->data as $data) {
            if (($data['tran_type'] == 'purchase' || $data['tran_type'] == 'verification')
                && $data['preauth'] == 'Y'
                && $data['transaction_status'] == 'approved'
            ) {
                return $data;
            } else {
                throw new \Exception('Nothing to capture');
            }
        }
    }
}
