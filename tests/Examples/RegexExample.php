<?php

$regex = '\/test\/\(w\+\)';
$url = '/test/a';

$match = preg_match('/\/test\/(?<id>\w+)/', $url, $matches);
print_r($matches);exit;
