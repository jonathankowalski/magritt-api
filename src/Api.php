<?php

namespace Magritt;

use GuzzleHttp\Client;

class Api
{
    const MAGRITT = 'https://api.magritt.io';

    protected $apikey;
    protected $secret;

    public function __construct($apikey, $secret)
    {
        $this->apikey = $apikey;
        $this->secret = $secret;
    }

    public function optimize($pathOrUrl)
    {
        if (!filter_var($pathOrUrl, FILTER_VALIDATE_URL) === false) {
            return $this->optimizeUrl($pathOrUrl);
        } else if(file_exists($pathOrUrl)) {
            return $this->optimizeFile($pathOrUrl);
        }
        throw new \InvalidArgumentException("$pathOrUrl is not an url nor a file");
    }

    public function optimizeUrl($url)
    {
        $http = new Client();
        $data = $this->getData(['url'=>$url]);
        $res = $this->requestOptimize($http, [
           'form_params' => ["data"=>json_encode($data)]
        ]);
        return (string) $res->getBody();
    }

    public function optimizeFile($path)
    {
        $http = new Client();
        $data = $this->getData([basename($path)]);
        $res = $this->requestOptimize($http, [
            'multipart' => [
                [
                    'name' => 'data',
                    'contents' => json_encode($data)
                ],
                [
                    'name' => 'upload',
                    'contents' => fopen($path, 'r')
                ]
            ]
        ]);
        return (string) $res->getBody();
    }

    protected function requestOptimize(Client$http, $data)
    {
        try {
            $res = $http->request('POST', self::MAGRITT.'/optimize', $data);
            return $res;
        } catch(\Exception $e) {
            echo $e->getResponse()->getBody(true);
            exit();
        }
    }

    protected function getData($data)
    {
        $signature = Hash::hash(implode('',$data), $this->secret);
        return array_merge($data,[
            "apikey" => $this->apikey,
            "signature" => $signature
        ]);
    }
}