<?php

require __DIR__ .'/../vendor/autoload.php';

$conf = new Cxsearch\Configuration;
$birt = new Cxsearch\Index($conf, 'birt');

$birt->newSearch()
	->query('Ford')
	->filterByLineGTE('Classic Cars')
	->andFilterByPriceLT(1000)
	->filterByFoo('aaa')
	->orQueryByTitle('aeee')
	->orQueryByBody('Weeeee', 4)
    ->prefixSuffix('description', 'PRE', 'SUF')
    ->sort(array(array('title' => 'asc'), array('year' => 'desc')))
	//->orQuery()
	->dump();