<?php

namespace App\Library\Services\Ipsp;
/**
 * Class PaymentClient
 */
class Client {
    private $id;
    private $password;
    private $url;
    public function __construct($id,$password,$domain){
        if(empty($id)) throw new \Exception('auth id not set');
        if(empty($password)) throw new \Exception('auth password not set');
        if(empty($domain)) throw new \Exception('ipsp gateway not set');
        $this->id = $id;
        $this->password = $password;
        $this->url = sprintf('https://%s/api',$domain);
    }
    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }
    /**
     * @return String
     */
    public function getPassword(){
        return $this->password;
    }
    /**
     * @return String
     */
    public function getUrl(){
        return $this->url;
    }
}
