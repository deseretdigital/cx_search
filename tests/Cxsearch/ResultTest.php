<?php
namespace Cxsearch;

use Cxsearch\Search\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $this->getDef_response = '{"documentCount":206,"operationCount":18,"name":"birt","configuration":{"index":{"number_of_replicas":0,"number_of_shards":1},"fields":{"scale":{"indexOps":"facet","type":"string"},"msrp":{"indexOps":"facet","type":"double"},"vendor":{"indexOps":"sort,facet","resultCfg":"hl","type":"string"},"line":{"indexOps":"facet,sort","type":"string"}}},"aliases":[]}';

        $this->getContent_response = '{"matches":[{"index":"birt","document":{"fields":{"scale":"1:12","msrp":"136.67","vendor":"Welly Diecast Productions","description":"Model features 30 windows, skylights & glare resistant glass, working steering system, original logos","name":"1958 Setra Bus","line":"Trucks and Buses","seqnum":8},"id":"S12_1666","version":1},"score":1.0,"sortValues":[]},{"index":"birt","document":{"fields":{"scale":"1:18","msrp":"142.25","vendor":"Min Lin Diecast","description":"This model features, opening hood, opening doors, detailed engine, rear spoiler, opening trunk, working steering, tinted windows, baked enamel finish. Color yellow.","name":"1995 Honda Civic","line":"Classic Cars","seqnum":24},"id":"S18_1984","version":1},"score":1.0,"sortValues":[]},{"index":"birt","document":{"fields":{"scale":"1:18","msrp":"62.17","vendor":"Studio M Art Models","description":"Features rotating wheels , working kick stand. Comes with stand.","name":"1957 Vespa GS150","line":"Motorcycles","seqnum":44},"id":"S18_3782","version":1},"score":1.0,"sortValues":[]}],"start":0,"totalMatched":3,"annotations":{},"facets":{}}';

        $this->baseUrl = 'http://sandbox.cxsearch.cxense.com';

        $this->index = $this->getCxIndex();
    }

    protected function getCxIndex($successful=TRUE, $id='birt')
    {
        $conf = new Configuration;
        $conf->setBaseUrl($this->baseUrl);

        $index = $this->getMock('Cxsearch\Index', array('getDef', 'getBrowser'), array($conf, $id));
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

        $index->expects($this->any())
            ->method('getBrowser')
            ->will($this->returnValue($newBrowser));

        return $index;
    }

    public function testFacetReturned()
    {
        $inputData = '{"matches":[{"index":"birt","document":{"fields":{"vendor":"Studio M Art Models","line":"Classic Cars","seqnum":52,"msrp":"71.27","name":"1957 Ford Thunderbird","scale":"1:18","description":"This 1:18 scale precision die-cast replica, with its optional porthole hardtop and factory baked-enamel Thunderbird Bronze finish, is a 100% accurate rendition of this American classic."},"id":"S18_4933","version":2},"score":"NaN","sortValues":[71.27],"highlights":{"description":["-enamel Thunderbird Bronze finish, is a 100% accurate rendition of this American <b>classic</b>."]}},{"index":"birt","document":{"fields":{"vendor":"Highway 66 Mini Classics","line":"Classic Cars","seqnum":38,"msrp":"107.57","name":"1985 Toyota Supra","scale":"1:18","description":"This model features soft rubber tires, working steering, rubber mud guards, authentic Ford logos, detailed undercarriage, opening doors and hood, removable split rear gate, full size spare mounted in bed, detailed interior with opening glove box"},"id":"S18_3233","version":2},"score":"NaN","sortValues":[107.57],"highlights":{"description":["This model features soft rubber tires, working steering, rubber mud guards, authentic <b>Ford</b> logos"]}},{"index":"birt","document":{"fields":{"vendor":"Gearbox Collectibles","line":"Classic Cars","seqnum":42,"msrp":"146.99","name":"1976 Ford Gran Torino","scale":"1:18","description":"Highly detailed 1976 Ford Gran Torino \"Starsky and Hutch\" diecast model. Very well constructed and painted in red and white patterns."},"id":"S18_3482","version":2},"score":"NaN","sortValues":[146.99],"highlights":{"description":["Highly detailed 1976 <b>Ford</b> Gran Torino \"Starsky and Hutch\" diecast model. Very well constructed and painted in red and white patterns."]}},{"index":"birt","document":{"fields":{"vendor":"Second Gear Diecast","line":"Classic Cars","seqnum":12,"msrp":"173.02","name":"1969 Ford Falcon","scale":"1:12","description":"Turnable front wheels; steering function; detailed interior; detailed engine; opening hood; opening trunk; opening doors; and detailed chassis."},"id":"S12_3891","version":2},"score":"NaN","sortValues":[173.02]},{"index":"birt","document":{"fields":{"vendor":"Autoart Studio Design","line":"Classic Cars","seqnum":6,"msrp":"194.57","name":"1968 Ford Mustang","scale":"1:12","description":"Hood, doors and trunk all open to reveal highly detailed interior features. Steering wheel actually turns the front wheels. Color dark green."},"id":"S12_1099","version":2},"score":"NaN","sortValues":[194.57]}],"start":0,"totalMatched":5,"annotations":{},"facets":{"vendor":[{"label":"Studio M Art Models","count":1},{"label":"Second Gear Diecast","count":1},{"label":"Highway 66 Mini Classics","count":1},{"label":"Gearbox Collectibles","count":1},{"label":"Autoart Studio Design","count":1}],"line":[{"label":"Classic Cars","count":5}],"scale":[{"label":"1:18","count":3},{"label":"1:12","count":2}]}}';

        $resultObj = new Result($this->index, json_decode($inputData));

        $this->assertTrue((bool)$resultObj->getFacets());
    }
}