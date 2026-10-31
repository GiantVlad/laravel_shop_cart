<?php

namespace App\Library\Services\Ipsp;

use Exception;

/**
 * Class Api
 */
class Api
{
    private Client $client;
    private array $params = [];
    
    /**
     * Supported currencies
     */
    const UAH = 'UAH';
    const USD = 'USD';
    const EUR = 'EUR';
    const RUB = 'RUB';
    const GBP = 'GBP';

    public function __construct ()
    {
        defined('MERCHANT_ID') or define('MERCHANT_ID' , '1396424');
        defined('MERCHANT_PASSWORD') or define('MERCHANT_PASSWORD' , 'test');
        defined('IPSP_GATEWAY') or define('IPSP_GATEWAY' , 'api.fondy.eu');
        
        $this->client = new Client(MERCHANT_ID, MERCHANT_PASSWORD, IPSP_GATEWAY);
    }

    /**
     * @param string $name
     * @return Resource
     * @throws Exception
     */
    public function initResource(string $name): Resource
    {
        $class = __NAMESPACE__.'\\Resource\\' . ucfirst($name);
        if (!class_exists($class)) {
            throw new Exception(sprintf('ipsp resource "%s" not found', $class));
        }
        return new $class();
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function call(string $name, array $params = [])
    {
        $resource = $this->initResource($name);
        $resource->setClient($this->client);
        return $resource->call(array_merge($this->params, $params));
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setParam(string $key = '', string $value = ''): void
    {
        $this->params[$key] = $value;
    }
    
    /**
     * @param string $key
     * @return array
     */
    public function getParam(string $key = ''): array
    {
        return $this->params[$key];
    }
    
    public function hasAcsData()
    {
        return isset($_POST['MD']) AND isset($_POST['PaRes']);
    }

    public function hasResponseData()
    {
        return isset($_POST['response_status']);
    }

    public function success($callback)
    {

    }

    public function failure($callback)
    {

    }

}
