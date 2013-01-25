<?php

require dirname(__file__).'/../vendor/autoload.php';

$birt = new Cxsearch\Index('birt');
$birt->setBaseUrl('http://sandbox.cxsearch.cxense.com');

$birt->newSearch()
	->query('Ford')
	->filterByLineGTE('Classic Cars')
	->andFilterByPriceLT(1000)
	->filterByFoo('aaa')
	->orQueryByTitle('aeee')
	->orQueryByBody('Weeeee', 4)
	//->orQuery()
	->dump();