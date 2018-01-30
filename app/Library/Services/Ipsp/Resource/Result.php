<?php

namespace App\Library\Services\Ipsp\Resource;

use App\Library\Services\Ipsp\Resource;
/**
 * Class PaymentResult
 */
class Result extends Resource{
    public function call( $data = NULL ){
        if( empty( $data ) )
        {
            $this->parseResponseData();
        } else{
            $this->setResponse($data);
        }
        return $this;
    }
    private function parseResponseData(){
       $body = file_get_contents('php://input');
       $types = $this->request->getContentTypes();
       $types = array_flip($types);
       $type = explode(';',$_SERVER['CONTENT_TYPE']);
       $type = trim($type[0]);
       if(isset($types[$type])){
           $this->format = $types[$type];
           $data = $this->parseRespose($body);
           $this->setResponse($data);
       }
    }
}