<?php

namespace Cxsearch;

use Buzz\Browser;

class IndexTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->baseUrl = 'http://sandbox.cxsearch.cxense.com';
        $this->index = new Index('birt');
        $this->index->setBaseUrl($this->baseUrl);
    }

    /**
     * @covers Cxsearch\Index::__construct
     * @covers Cxsearch\Index::getId
     * @covers Cxsearch\Index::setBrowser
     */
    public function testGetId()
    {
        $this->assertEquals('birt', $this->index->getId());
    }

    /**
     * @covers Cxsearch\Index::setBaseUrl
     * @covers Cxsearch\Index::getBaseUrl
     */
    public function testSetBaseUrl()
    {
        $this->assertEquals($this->baseUrl, $this->index->getBaseUrl());
    }

    /**
     * @covers Cxsearch\Index::__construct
     */
    public function testNewIndexWithCustomBrowser()
    {
        $index = new Index('birt', new Browser);
    }

    /**
     * @covers Cxsearch\Index::getDef
     * @covers Cxsearch\Index::buildUrl
     */
    public function testGetDef()
    {
        $def = $this->index->getDef(TRUE);
        $this->assertObjectHasAttribute('configuration', $def);
    }

    /**
     * @covers Cxsearch\Index::getBrowser
     */
    public function testGetBrowser()
    {
        $browser = $this->index->getBrowser();
        $this->assertInstanceOf('Buzz\Browser', $browser);
    }

    /**
     * @covers Cxsearch\Index::getDocument
     */
    public function testGetDocument()
    {
        $doc = $this->index->getDocument('2007_SUBY_WRX_STI');
        $this->assertInstanceOf('Cxsearch\Document', $doc);
    }

    /**
     * @covers Cxsearch\Index::newDocument
     */
    public function testNewDocument()
    {
        $doc = $this->index->newDocument('2013_SUBY_WRX_STISSSS');
        $this->assertInstanceOf('Cxsearch\Document', $doc);
        $this->assertTrue($doc->isNew());
    }

    /**
     * @covers Cxsearch\Index::newSearch
     */
    public function testNewSearh()
    {
        $search = $this->index->newSearch();
        $this->assertInstanceOf('Cxsearch\Search', $search);
    }
}