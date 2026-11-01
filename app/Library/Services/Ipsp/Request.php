<?php

namespace App\Library\Services\Ipsp;

use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class Request
 */
class Request
{
    private $curl;
    private $format;
    private $contentType = array(
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'form' => 'application/x-www-form-urlencoded'
    );
    
    public function __construct()
    {
        $this->curl = new Curl;
    }

    /**
     * @param string $format
     * @return Request $this
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param string $format
     * @return string
     */
    private function getContentType(string $format)
    {
        $type = $this->contentType[$format];
        return  sprintf('Content-Type: %s',$type);
    }

    /**
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentType;
    }

    /**
     * @param string $str
     * @return string
     */
    private function getContentLength(string $str=''): string
    {
        return sprintf('Content-Length: %s', strlen($str));
    }

    /**
     * @param string $url
     * @param string $params
     * @return bool|string
     */
    public function doPost(string $url = '' , string $params = '')
    {
        $this->curl->create($url);
        $this->curl->ssl(false, config('payments.fondy.verify_ssl_host', true) ? 2 : 0);
        $this->curl->post($params);
        $this->curl->http_header($this->getContentType( $this->format ));
        $this->curl->http_header($this->getContentLength($params));
        
        return $this->execute($url);
    }

    /**
     * @param string $url
     * @param array $params
     * @return bool|mixed
     */
    public function doGet(string $url='', array $params = [])
    {
        $this->curl->create(
            $url.(empty($params) ? '' : '?'.http_build_query($params))
        );
        $this->curl->ssl(false, config('payments.fondy.verify_ssl_host', true) ? 2 : 0);
        $this->curl->http_header($this->getContentType( $this->format ));
        $this->curl->http_header($this->getContentLength(implode('', $params)));
        return $this->execute($url);
    }

    public function getStatusCode(): ?int
    {
        $statusCode = $this->curl->info['http_code'] ?? null;

        return $statusCode === null ? null : (int) $statusCode;
    }

    private function execute(string $url): string
    {
        $response = $this->curl->execute();

        if ($response === false) {
            $statusCode = $this->getStatusCode();
            $errorCode = (int) $this->curl->error_code;
            $errorMessage = $this->curl->error_string ?: 'Unknown cURL error';

            Log::error('Fondy API request failed.', [
                'url' => $url,
                'http_status' => $statusCode,
                'curl_error_code' => $errorCode,
                'curl_error' => $errorMessage,
            ]);

            throw new RuntimeException(sprintf(
                'Fondy API request failed (HTTP status: %s, cURL error %d: %s).',
                $statusCode ?? 'unknown',
                $errorCode,
                $errorMessage,
            ));
        }

        return $response;
    }
}
