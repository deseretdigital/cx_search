<?php

require dirname(__file__).'/../vendor/autoload.php';

$birt = new Cxsearch\Index('birt');
$birt->setBaseUrl('http://sandbox.cxsearch.cxense.com');

$ret = $birt->getDef();

var_dump($ret);