<?php

namespace Cxsearch;

/**
* 
*/
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->baseUrl = 'http://sandbox.cxsearch.cxense.com';
        $conf = new Configuration;
        $this->index = new Index($conf, 'birt');
    }

    /**
     * @covers Cxsearch\Document::__construct
     * @covers Cxsearch\Document::load
     * @covers Cxsearch\Document::isNew
     * @covers Cxsearch\Document::__get
     * @covers Cxsearch\Document::__set
     */
    public function testLoad()
    {
        $id = 'S18_4933';
        $doc = Document::load($this->index, $id);

        $this->assertEquals($id, $doc->id);
        $this->assertInstanceOf('Cxsearch\Document', $doc);
        $this->assertFalse($doc->isNew());
        $this->assertFalse($doc->__set('id', 'NOVO'));

        return $doc->getData();
    }

    /**
     * @covers Cxsearch\Document::create
     * @covers Cxsearch\Document::skeleton
     * @covers Cxsearch\Document::__set
     * @covers Cxsearch\Document::__construct
     * @covers Cxsearch\Document::__get
     */
    public function testCreate()
    {
        $id = '123_testando';
        $doc = Document::create($this->index, $id);

        $this->assertTrue($doc->isNew());
        $this->assertInstanceOf('Cxsearch\Document', $doc);
        $this->assertEquals($id, $doc->id);

        $scale = "10:3";
        $doc->scale = $scale;
    }

    /**
     * @depends testLoad
     * @covers Cxsearch\Document::materialize
     * @covers Cxsearch\Document::__get
     * @covers Cxsearch\Document::__isset
     */
    public function testMaterialize($raw)
    {
        $id = 'S18_4933';
        $doc = Document::materialize($this->index, $raw);

        $this->assertEquals($id, $doc->id);
        $this->assertInstanceOf('Cxsearch\Document', $doc);
        $this->assertFalse($doc->isNew());
        $this->assertEquals($raw->fields->scale, $doc->scale);

        $x = @$doc->fooobar;
    }

    /**
     * @depends testLoad
     * @covers Cxsearch\Document::getData
     */
    public function testGetData($raw)
    {
        $id = 'S18_4933';
        $doc = Document::materialize($this->index, $raw);

        $this->assertEquals($raw, $doc->getData());
    }

    /**
     * @depends testLoad
     * @covers Cxsearch\Document::save
     */
    public function testSavingNothing($raw)
    {
        $id = 'S18_4933';
        $doc = Document::materialize($this->index, $raw);
        $this->assertNull($doc->save());
    }

    /**
     * @covers Cxsearch\Document::save
     * @covers Cxsearch\Document::buildUrl
     */
    public function testSavingNotNewDoc()
    {
        $id = 'MyPHPTestId_' . time();
        $doc = Document::create($this->index, $id);
        $this->assertNull($doc->save());

        return $doc;
    }

    /**
     * @depends testSavingNotNewDoc
     * @covers Cxsearch\Document::save
     * @covers Cxsearch\Document::_saved
     * @covers Cxsearch\Document::buildUrl
     */
    public function testSave($doc)
    {
        $doc->scale  = '1:20';
        $doc->msrp   = 124.2;
        $doc->vendor = "DISNAC";
        $doc->line = "SVU";
        $doc->description = "bla bla bla";
        $doc->name = "2012 Veloster";

        $this->assertTrue($doc->save());

        return $doc;
    }

    /**
     * @depends testSave
     * @covers Cxsearch\Document::save
     */
    public function testSave2($doc)
    {
        $doc->name = "Veloster 2012";
        $this->assertTrue($doc->save());

        return $doc;   
    }

    /**
     * @depends testSave2
     * @covers Cxsearch\Document::delete
     * @covers Cxsearch\Document::buildUrl
     */
    public function testDeleteDoc($doc)
    {
        $doc->delete();
    }

    /**
     * @covers Cxsearch\Document::delete
     */
    public function testDeleteNewDoc()
    {
        $id = 'MyPHPTestId_' . time();
        $doc = Document::create($this->index, $id);
        $this->assertNull($doc->delete());
    }

    /**
     * @covers Cxsearch\Document::save
     */
    public function testSavingNewDocError()
    {
        $newIndex = clone $this->index;

        $newResponse = $this->getMockBuilder('Buzz\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $newResponse->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(FALSE));

        $newBrowser = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();
 
        $newBrowser->expects($this->any())
            ->method('post')
            ->will($this->returnValue($newResponse));

        $newIndex->setBrowser($newBrowser);

        $id = 'MyPHPTestId2_' . time();
        $doc = Document::create($newIndex, $id);
        $doc->scale  = '1:20';
        $doc->msrp   = 124.2;
        $doc->vendor = "DISNAC";
        $doc->line = "SVU";
        $doc->description = "bla bla bla";
        $doc->name = "2012 Veloster";
        $this->assertFalse($doc->save());
    }

    /**
     * @depends testLoad
     * @covers Cxsearch\Document::save
     */
    public function testSavingDocError($raw)
    {
        $newIndex = clone $this->index;

        $newResponse = $this->getMockBuilder('Buzz\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $newResponse->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(FALSE));

        $newBrowser = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();
 
        $newBrowser->expects($this->any())
            ->method('post')
            ->will($this->returnValue($newResponse));

        $newIndex->setBrowser($newBrowser);

        $id = 'S18_4933';
        $doc = Document::materialize($newIndex, $raw);
        //var_dump($doc);
        $doc->scale  = '122:20';
        
        $this->assertFalse($doc->save());
    }
}