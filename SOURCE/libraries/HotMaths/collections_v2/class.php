<?php

/**
 * HOTMATHS API CLASS COLLECTION
 */

Loader::library('HotMaths/apiv3');

class HMClass extends NewerHotMathsAPI
{
    public function getClassByClassKey($classKey)
    {
        $url = $this->curlLink . '/api/class/find/code/' . $classKey . $this->tokenURL;
        return $this->requestHMAPI($url);
    }
}
