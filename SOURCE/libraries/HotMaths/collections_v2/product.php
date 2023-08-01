<?php

/**
 * HOTMATHS API PRODUCT COLLECTION
 * ANZGO-3914 Added by Shane Camus 11/23/18
 */

Loader::library('HotMaths/apiv3');

class HMProduct extends NewerHotMathsAPI
{
    public function getAllProducts()
    {
        $url = $this->curlLink . '/api/product/products/' . $this->tokenURL;
        return $this->requestHMAPI($url);
    }

    public function getProduct($hmPID)
    {
        $url = $this->curlLink . '/api/product/' . $hmPID . $this->tokenURL;
        return $this->requestHMAPI($url);
    }
}
