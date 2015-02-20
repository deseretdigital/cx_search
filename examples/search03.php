<?php

require __DIR__ .'/../vendor/autoload.php';

$conf = new Cxsearch\Configuration;
$birt = new Cxsearch\Index($conf, 'birt');

$birt->newSearch()
    ->query('Ford')
    ->addFacetGroup(
        array(
            array(
                'fieldName' => 'vendor',
                'depth'     => 'all'
            ),
            array(
                'fieldName' => 'scale',
                'depth'     => 10
            ),
            array(
                'fieldName' => 'line',
                'depth'     => 100
            )
        )
    )
    ->andQueryByLine('Classic Cars')
    ->andFilterByMsrpGT(20)
    ->prefixSuffix('description', '<b>', '</b>')
    ->sort(array('msrp' => 'asc'))
    ->run($result);

var_dump($result->getFacets());
