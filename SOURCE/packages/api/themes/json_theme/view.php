<?php

header('Content-Type: application/json');
echo !empty($result) ? json_encode($result) : '';