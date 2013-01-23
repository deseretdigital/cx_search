<?php

namespace Cxsearch;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
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
        $index = new Index('birt');
        $index->setBaseUrl('http://sandbox.cxsearch.cxense.com');
        $this->assertEquals('http://sandbox.cxsearch.cxense.com', $index->getBaseUrl());
    }

    /**
     * @covers Cxsearch\Index::getDef
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
}