<?php

require __DIR__.'/../vendor/autoload.php';

$conf = new Cxsearch\Configuration;
$birt = new Cxsearch\Index($conf);

$birt->setIndex('birt');

$doc1 = $birt->getDocument('2007_SUBY_WRX_STI');

$doc1->quantity = 41;
var_dump($doc1->id = 'asdsda');
$doc1->vendor = "CodeCraft";
$doc1->newField = "Fooobar";
var_dump($doc1, $doc1->id, $doc1->name, $doc1->quantity);

$doc2 = Cxsearch\Document::create($birt, '2012_AAA_QWE_STS');
$doc2->scale = 2.2;
var_dump($doc2, isset($doc2->id), $doc2->id, $doc2->scale);