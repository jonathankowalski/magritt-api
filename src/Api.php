<?php

namespace Rembrand;

use GuzzleHttp\Client;

class Api
{
    const REMBRAND = 'https://rembrand.io';

    protected $apikey;
    protected $secret;

    public function __construct($apikey, $secret)
    {
        $this->apikey = $apikey;
        $this->secret = $secret;
    }

    public function optimize($url)
    {
        $http = new Client();
        $data = $this->getData(['url'=>$url]);
        $res = $http->request('POST', self::REMBRAND.'/optimize',[
           'form_params' => ["data"=>json_encode($data)]
        ]);
        return (string) $res->getBody();
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