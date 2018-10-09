<?php

namespace App\FRest;

use Exception;

/**
 * Description of FRest
 *
 * @author davidcallizaya
 */
class FRest
{
    protected $path = '/documents/1';
    protected $method = 'GET';
    protected $headers = [];
    protected $cacheTime = 120;
    protected $body = [];
    protected $sample = [];

    private function getUrl()
    {
        return env('SCI_URL_BASE') . $this->path;
    }

    private function getMethod()
    {
        return $this->method;
    }

    private function getHeaders()
    {
        return $this->headers;
    }

    public function call()
    {
        if (env('SCI_DEMO')) {
            return $this->sample;
        }
        $curl = curl_init();
        $options = array(
//            CURLOPT_PORT           => "40443",//"40448",
            CURLOPT_URL            => $this->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $this->getMethod(),
            CURLOPT_HTTPHEADER     => $this->getHeaders(),
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        );
        if ($this->getMethod() === 'POST') {
            $options[CURLOPT_POSTFIELDS] = json_encode($this->body);
        }
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            throw new Exception("cURL Error #:" . $err);
        } else {
            $json = @json_decode($response);
			if ($json===null) {
				die($response);
			}
			return $json;
        }
    }
}
