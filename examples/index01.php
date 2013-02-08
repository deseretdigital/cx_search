<?php

require __DIR__ .'/../vendor/autoload.php';

$conf = new Cxsearch\Configuration;
$birt = new Cxsearch\Index($conf, 'birt');

$ret = $birt->getDef();

var_dump($ret);