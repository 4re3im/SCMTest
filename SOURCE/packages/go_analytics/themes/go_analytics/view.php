<?php

if (isset($useJSON) && $useJSON) {
    header('Content-Type: application/json');
}

if (isset($result) && $result) {
    echo $result;
}
