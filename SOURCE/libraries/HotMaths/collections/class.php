<?php

/**
 * HOTMATHS API CLASS COLLECTION
 */

Loader::library('HotMaths/apiv2');

class HMClass extends NewHotMathsAPI
{
    public function getClassByClassKey($classKey)
    {
        $url = $this->curlLink . '/api/class/find/code/' . $classKey . $this->tokenURL;
        return $this->requestHMAPI($url);
    }
}
