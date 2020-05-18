<?php

namespace App\Library\Services\Ipsp;
/**
 * Class Curl
 */
class Curl {
    protected $response = '';       // Contains the cURL response for debug
    protected $last_response;
    protected $session;             // Contains the cURL handler for a session
    protected $url;                 // URL of the session
    protected $options = array();   // Populates curl_setopt_array
    protected $headers = array();   // Populates extra HTTP headers
    public $error_code;             // Error code returned as an int
    public $error_string;           // Error message returned as a string
    public $info;                   // Returned after request (elapsed time, etc)
    function __construct($url = '')
    {
        if (!$this->is_enabled()) {
            throw new \Exception('curl module not found');
        }
        $this->url = $url;
    }
    public function post($params=array(),$options=array())
    {
        if (is_array($params)) {
            $params = http_build_query($params, NULL, '&');
        }
        $this->options($options);
        $this->http_method('post');
        $this->option(CURLOPT_POST, TRUE);
        $this->option(CURLOPT_POSTFIELDS, $params);
    }
    public function put($params = array(), $options = array())
    {
        if (is_array($params))
            $params = http_build_query($params, NULL, '&');
        $this->options($options);
        $this->http_method('put');
        $this->option(CURLOPT_POSTFIELDS, $params);
        $this->option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));
    }
    public function patch($params = array(), $options = array())
    {
        if (is_array($params))
            $params = http_build_query($params, NULL, '&');

        $this->options($options);
        $this->http_method('patch');
        $this->option(CURLOPT_POSTFIELDS, $params);
        $this->option(CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PATCH'));
    }
    public function delete($params, $options = array())
    {
        if (is_array($params))
            $params = http_build_query($params, NULL, '&');
        $this->options($options);
        $this->http_method('delete');
        $this->option(CURLOPT_POSTFIELDS, $params);
    }
    public function set_cookies($params = array())
    {
        if (is_array($params))
        {
            $params = http_build_query($params, NULL, '&');
        }
        $this->option(CURLOPT_COOKIE, $params);
        return $this;
    }
    public function http_header($header, $content = NULL)
    {
        $this->headers[] = $content ? $header . ': ' . $content : $header;
        return $this;
    }
    public function http_method($method)
    {
        $this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        return $this;
    }
    public function http_login($username = '', $password = '', $type = 'any')
    {
        $this->option(CURLOPT_HTTPAUTH, constant('CURLAUTH_' . strtoupper($type)));
        $this->option(CURLOPT_USERPWD, $username . ':' . $password);
        return $this;
    }
    public function proxy($url = '', $port = 80)
    {
        $this->option(CURLOPT_HTTPPROXYTUNNEL, TRUE);
        $this->option(CURLOPT_PROXY, $url . ':' . $port);
        return $this;
    }
    public function proxy_login($username = '', $password = '')
    {
        $this->option(CURLOPT_PROXYUSERPWD, $username . ':' . $password);
        return $this;
    }
    //In original Class it was "$verify_peer = TRUE"
    public function ssl($verify_peer = false, $verify_host = 2, $path_to_cert = NULL)
    {
        if ($verify_peer)
        {
            $this->option(CURLOPT_SSL_VERIFYPEER, TRUE);
            $this->option(CURLOPT_SSL_VERIFYHOST, $verify_host);
            if (isset($path_to_cert)) {
                $path_to_cert = realpath($path_to_cert);
                $this->option(CURLOPT_CAINFO, $path_to_cert);
            }
        }
        else
        {
            $this->option(CURLOPT_SSL_VERIFYPEER, FALSE);
            $this->option(CURLOPT_SSL_VERIFYHOST, $verify_host);
        }
        return $this;
    }
    public function options($options = array())
    {
        foreach ($options as $option_code => $option_value) {
            $this->option($option_code, $option_value);
        }
        curl_setopt_array($this->session, $this->options);
        return $this;
    }
    public function option($code, $value, $prefix = 'opt')
    {
        if (is_string($code) && !is_numeric($code))
            $code = constant('CURL' . strtoupper($prefix) . '_' . strtoupper($code));
        $this->options[$code] = $value;
        return $this;
    }
    public function create($url)
    {
        $this->url = $url;
        $this->session = curl_init($this->url);
        return $this;
    }
    public function execute()
    {
        if ( ! isset($this->options[CURLOPT_TIMEOUT]))
        {
            $this->options[CURLOPT_TIMEOUT] = 60;
        }
        if ( ! isset($this->options[CURLOPT_RETURNTRANSFER]))
        {
            $this->options[CURLOPT_RETURNTRANSFER] = TRUE;
        }
        if ( ! isset($this->options[CURLOPT_FAILONERROR]))
        {
            $this->options[CURLOPT_FAILONERROR] = TRUE;
        }
        if ( ! ini_get('safe_mode') && ! ini_get('open_basedir'))
        {
            if ( ! isset($this->options[CURLOPT_FOLLOWLOCATION]))
            {
                $this->options[CURLOPT_FOLLOWLOCATION] = TRUE;
            }
        }
        if ( ! empty($this->headers))
        {
            $this->option(CURLOPT_HTTPHEADER, $this->headers);
        }
        $this->options();
        curl_setopt($this->session, CURLOPT_VERBOSE, true);

        $verbose = fopen('php://temp', 'w+');
        curl_setopt($this->session, CURLOPT_STDERR, $verbose);
        $this->response = curl_exec($this->session);
        $this->info = curl_getinfo($this->session);
        if ($this->response === FALSE)
        {

            $errno = curl_errno($this->session);
            $error = curl_error($this->session);
            curl_close($this->session);
            $this->set_defaults();
            $this->error_code = $errno;
            $this->error_string = $error;
            return FALSE;
        }
        else
        {
            curl_close($this->session);
            $this->last_response = $this->response;
            $this->set_defaults();
            return $this->last_response;
        }
    }
    public function is_enabled()
    {
        return function_exists('curl_init');
    }
    public function set_defaults()
    {
        $this->response = '';
        $this->headers = array();
        $this->options = array();
        $this->error_code = NULL;
        $this->error_string = '';
        $this->session = NULL;
    }
}
