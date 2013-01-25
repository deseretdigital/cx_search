<?php

require dirname(__file__).'/../vendor/autoload.php';

$birt = new Cxsearch\Index('birt');
$birt->setBaseUrl('http://sandbox.cxsearch.cxense.com');

$result = null;
$birt->newSearch()
    ->query('Ford')
    ->andQueryByLine('Classic Cars')
    ->andFilterByMsrpGT(20)
    ->run($result);

 var_dump($result);