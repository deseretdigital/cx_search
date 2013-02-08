<?php

require dirname(__file__).'/../vendor/autoload.php';

$conf = new Cxsearch\Configuration;
$birt = new Cxsearch\Index($conf, '_all');

$result = null;
$birt->newSearch()
    ->query('Ford')
    ->andQueryByLine('Classic Cars')
    ->andFilterByMsrpGT(20)
    ->prefixSuffix('description', '<b>', '</b>')
    ->sort(array('msrp' => 'asc'))
    ->run($result);

var_dump($result->length());
foreach ($result as $match) {
    var_dump($match->doc->id);
    var_dump($match->getIndex()->getId());
}