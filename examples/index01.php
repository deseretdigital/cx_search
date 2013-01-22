<?php

require dirname(__file__).'/../vendor/autoload.php';

$birt = new Cxsearch\Index('birt');
$birt->setBaseUrl('http://sandbox.cxsearch.cxense.com');

$ret = $birt->getDef();

//var_dump($ret);

$doc1 = $birt->getDocument('2007_SUBY_WRX_STI');
$doc1->quantity = 41;
$doc1->id = 'asdsda';
$doc1->vendor = "CodeCraft";
$doc1->newField = "Fooobar";
var_dump($doc1, $doc1->id, $doc1->name, $doc1->quantity);

$doc2 = Cxsearch\Document::create($birt, '2012_AAA_QWE_STS');
$doc2->scale = 2.2;
//var_dump($doc2, isset($doc2->id), $doc2->id, $doc2->scale);