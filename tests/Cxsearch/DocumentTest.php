<?php

namespace Cxsearch;

/**
* 
*/
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->getDef_response = '{"documentCount":206,"operationCount":18,"name":"birt","configuration":{"index":{"number_of_replicas":0,"number_of_shards":1},"fields":{"scale":{"indexOps":"facet","type":"string"},"msrp":{"indexOps":"facet","type":"double"},"vendor":{"indexOps":"sort,facet","resultCfg":"hl","type":"string"},"line":{"indexOps":"facet,sort","type":"string"}}},"aliases":[]}';
        $this->getContent_response = '{"fields":{"scale":"1:18","msrp":"71.27","description":"This 1:18 scale precision die-cast replica, with its optional porthole hardtop and factory baked-enamel Thunderbird Bronze finish, is a 100% accurate rendition of this American classic.","vendor":"Studio M Art Models","name":"1957 Ford Thunderbird","line":"Classic Cars","seqnum":52},"id":"S18_4933","version":1}';
        
        $this->baseUrl = 'http://sandbox.cxsearch.cxense.com';
        
        $this->index = $this->getCxIndex();
    }
    
    protected function getCxIndex($successful=TRUE)
    {
        $conf = new Configuration;
		$conf->setBaseUrl($this->baseUrl);
        
        $index = $this->getMock('Cxsearch\Index', array(), array($conf, 'birt'));
        $index->expects($this->any())
            ->method('getDef')
                ->will($this->returnValue(json_decode($this->getDef_response)));
        
        $newResponse = $this->getMockBuilder('Buzz\Message\Response')
            ->disableOriginalConstructor()->getMock();
        
        $newResponse->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue($successful));
        
        $newResponse->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue($this->getContent_response));

        $newBrowser = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()->getMock();
 
        $newBrowser->expects($this->any())
            ->method('get')
            ->will($this->returnValue($newResponse));
        
        $newBrowser->expects($this->any())
            ->method('post')
            ->will($this->returnValue($newResponse));
        
        $newBrowser->expects($this->any())
            ->method('delete')
            ->will($this->returnValue($newResponse));
        
        $index->expects($this->any())
            ->method('getBrowser')
            ->will($this->returnValue($newBrowser));
        
        return $index;
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
     * @covers Cxsearch\Document::delete
     * @covers Cxsearch\Document::_saved
     * @covers Cxsearch\Document::buildUrl
     */
    public function testSave($doc)
    {
        $doc = Document::create($this->index, $doc->id);
        
        $doc->scale  = '1:20';
        $doc->msrp   = 124.2;
        $doc->vendor = "DISNAC";
        $doc->line = "SVU";
        $doc->description = "bla bla bla";
        $doc->name = "2012 Veloster";

        $this->assertTrue($doc->save());
        
        // Saving an not new document
        $doc->name = "Veloster 2012";
        $this->assertTrue($doc->save());
        
        // Deleting
        $this->assertTrue($doc->delete());

        return $doc;
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
        $index = $this->getCxIndex(FALSE);

        $id = 'MyPHPTestId2_' . time();
        $doc = Document::create($index, $id);
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
        $index = $this->getCxIndex(FALSE);

        $id = 'S18_4933';
        $doc = Document::materialize($index, $raw);
        $doc->scale  = '122:20';
        
        $this->assertFalse($doc->save());
    }

    /**
     * @covers Cxsearch\Document::addField
     */
    public function testAddField()
    {
        $id = 'S18_4933';
        $field = 'new_field';
        $value = 'test_value';

        $doc = Document::load($this->index, $id);
        $doc->addField($field, $value);

        $this->assertEquals($doc->__get($field), $value, 'Adding new field is not working');
    }
}