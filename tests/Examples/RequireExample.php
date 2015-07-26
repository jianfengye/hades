<?php

define('HADES_ROOT', '/vagrant/develop');

$dao = require(HADES_ROOT . '/config/dao.php');

function showDao($config)
{
    print_r($config);
}

//showDao($dao);

showDao( require(HADES_ROOT . '/config/dao.php') );
