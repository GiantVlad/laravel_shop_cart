<?php

namespace Ipsp\Resource;

use Ipsp\Resource;
/**
 * Class PaymentPcidss
 */
class Pcidss extends Resource
{
    protected $path = '/3dsecure_step1';
    protected $fields = array(
        'order_id' => array(
            'type' => 'string',
            'required' => TRUE
        ),
        'order_desc' => array(
            'type' => 'string',
            'required' => TRUE
        ),
        'currency' => array(
            'type' => 'string',
            'required' => TRUE
        ),
        'amount' => array(
            'type' => 'datetime',
            'required' => TRUE
        ),
        'card_number' => array(
            'type' => 'creditcard',
            'required' => TRUE
        ),
        'cvv2' => array(
            'type' => 'cvv',
            'required' => TRUE
        ),
        'expiry_date' => array(
            'type' => 'datetime',
            'required' => TRUE
        )
    );

    public function acsRedirect( ){
        $response = $this->response;
        if(!$response->acs_url) return FALSE;
        $data = array(
            'PaReq'   => $response->pareq,
            'MD'      => $response->md,
            'TermUrl' => $this->getParam('response_url')
        );
        $html = "<html><body><form id='form' action='$response->acs_url' method='post'>";
        foreach ($data as $key => $value)
            $html .= "<input type='hidden' name='$key' value='$value'>";
        $html .= "</form><script>document.getElementById('form').submit();</script>";
        $html .= "</body></html>";
        exit($html);
    }

}