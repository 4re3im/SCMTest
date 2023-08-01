<?php

/**
 * HOTMATHS API PRODUCT COLLECTION
 * ANZGO-3914 Added by Shane Camus 11/23/18
 */

Loader::library('HotMaths/apiv2');

class HMProduct extends NewHotMathsAPI
{
    protected $product;

    public function getProduct()
    {
        $url = $this->curlLink . '/api/product/' . $this->hmID . $this->tokenURL;
        $this->product = $this->requestHMAPI($url);
        return $this->product;
    }
}
