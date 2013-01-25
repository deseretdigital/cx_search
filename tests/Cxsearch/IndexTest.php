<?php

namespace Cxsearch;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @covers Cxsearch\Index::__construct
     * @covers Cxsearch\Index::getId
     */
    public function testGetId()
    {
        $index = new Index('birt');
        $this->assertEquals('birt', $index->getId());
    }

    /**
     * @covers Cxsearch\Index::setBaseUrl
     * @covers Cxsearch\Index::getBaseUrl
     */
    public function testSetBaseUrl()
    {
        $baseUrl = 'http://sandbox.cxsearch.cxense.com';
        $index = new Index('birt');
        $index->setBaseUrl($baseUrl);
        $this->assertEquals($baseUrl, $index->getBaseUrl());
    }

    /**
     * @covers Cxsearch\Index::getDef
     * @covers Cxsearch\Index::buildUrl
     */
    public function testGetDef()
    {
        $index = new Index('birt');
        $index->setBaseUrl('http://sandbox.cxsearch.cxense.com');
        $def = $index->getDef();
        $this->assertObjectHasAttribute('configuration', $def);
    }

    /**
     * @covers Cxsearch\Index::getDocument
     */
    public function testGetDocument()
    {
        $index = new Index('birt');
        $index->setBaseUrl('http://sandbox.cxsearch.cxense.com');
        $doc = $index->getDocument('2007_SUBY_WRX_STI');
        $this->assertInstanceOf('Cxsearch\Document', $doc);
    }

    /**
     * @covers Cxsearch\Index::newDocument
     */
    public function testNewDocument()
    {
        $index = new Index('birt');
        $index->setBaseUrl('http://sandbox.cxsearch.cxense.com');
        $doc = $index->newDocument('2013_SUBY_WRX_STISSSS');
        $this->assertInstanceOf('Cxsearch\Document', $doc);
        $this->assertTrue($doc->isNew());
    }
}