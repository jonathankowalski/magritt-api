<?php

namespace Rembrand;


class Hash
{
    public static function hash($data, $key)
    {
        return hash_hmac('sha1', $data, $key);
    }
}