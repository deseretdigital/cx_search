<?php

namespace Cxsearch;

use Buzz\Browser;
use Cxsearch\FacetGroup\FacetGroup;

/**
 * @group Search
 */
class SearchTest extends \PHPUnit_Framework_TestCase
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

    /**
     * @covers Cxsearch\Search::__construct
     * @covers Cxsearch\Search::query
     * @covers Cxsearch\Search::orQuery
     * @covers Cxsearch\Search::_prefix
     * @covers Cxsearch\Search::sort
     * @covers Cxsearch\Search::__call
     * @covers Cxsearch\Search::_callQuery
     * @covers Cxsearch\Search::_callFilter
     * @covers Cxsearch\Search::_query
     * @covers Cxsearch\Search::_filter
     * @covers Cxsearch\Search::filter
     * @covers Cxsearch\Search::orFilter
     * @covers Cxsearch\Search::prefixSuffix
     * @covers Cxsearch\Search::_buildQuery
     * @covers Cxsearch\Search::_addQuery
     * @covers Cxsearch\Search::lang
     * @covers Cxsearch\Search::start
     * @covers Cxsearch\Search::limit
     * @covers Cxsearch\Search::duplicateRemoval
     * @covers Cxsearch\Search::dump
     */
    public function testNewSearch()
    {
        $search = new Search($this->index);
        $this->assertInstanceOf('Cxsearch\Search', $search);

        $result = "";
        $expectedString = '?p_sm={"msrp":"asc"}&p_lang=en&p_s=1&p_c=4&p_dr=line&p_aq=query("Ford") OR query(scale^2:"1:21") AND query(line:"Classic Cars") AND filter(msrp>20) AND filter(vendor>"bubacar") OR filter(msrp<=40)&p_rs={"hl":{"description":{"p":"<b>","s":"<\/b>"}}}';

        $search->query('Ford')
            ->orQuery('scale', '1:21', 2)
            ->andQueryByLine('Classic Cars')
            ->andFilterByMsrpGT(20)
            ->filter('vendor', 'bubacar', '>')
            ->orFilter('msrp', 40, '<=')
            ->prefixSuffix('description', '<b>', '</b>')
            ->sort(array('msrp' => 'asc'))
            ->lang('en')
            ->start(1)
            ->limit(4)
            ->duplicateRemoval('line')
            ->dump($result);
        $result = urldecode($result);
        $this->assertEquals($expectedString,$result);
    }

    /**
     * @covers Cxsearch\Search::_buildQuery
     * @covers Cxsearch\Search::run
     * @covers Cxsearch\Search\Result::__construct
     * @covers Cxsearch\Search\Match::__construct
     */
    public function testRunSearch()
    {
        $search = new Search($this->index);
        $search->query('Ford')
            ->andQueryByLine('Classic Cars')
            ->andFilterByMsrpGT(20)
            ->prefixSuffix('description', '<b>', '</b>')
            ->sort(array('msrp' => 'asc'))
            ->run($result);

        $this->assertInstanceOf('Cxsearch\Search\Result', $result);

        return $result;
    }

    /**
     * @depends testRunSearch
     * @covers Cxsearch\Search\Result::getStart
     * @covers Cxsearch\Search\Result::length
     * @covers Cxsearch\Search\Result::rewind
     * @covers Cxsearch\Search\Result::current
     * @covers Cxsearch\Search\Result::key
     * @covers Cxsearch\Search\Result::next
     * @covers Cxsearch\Search\Result::valid
     */
    public function testResult($result)
    {
        $this->assertEquals($result->getStart(), 0);

        $i=0;
        foreach ($result as $key => $match) {
            $i++;
            $this->assertInstanceOf('Cxsearch\Search\Match', $match);
        }

        $this->assertEquals($i, $result->length());
    }

    /**
     * @depends testRunSearch
     * @covers Cxsearch\Search\Match::getIndex
     */
    public function testMatch($result)
    {
        $result->rewind();
        $match = $result->current();

        $this->assertInstanceOf('Cxsearch\Search\Match', $match);

        $this->assertInstanceOf('Cxsearch\Index', $match->getIndex());
    }

    /**
     * @covers Cxsearch\Search::run
     */
    public function testRunSearchFalse()
    {
        $index = $this->getCxIndex(FALSE);
        $search = new Search($index);
        $rs = $search->query('Ford')
            ->andQueryByLine('Classic Cars')
            ->andFilterByMsrpGT(20)
            ->prefixSuffix('description', '<b>', '</b>')
            ->sort(array('msrp' => 'asc'))
            ->run($result);

        $this->assertFalse($rs);
    }

   /**
    * @covers Cxsearch\Search::dump
    */
   public function testNewSearchDump()
   {
       $search = new Search($this->index);
       $this->assertInstanceOf('Cxsearch\Search', $search);

       $expectedResult = 'string(376) "?p_rs=%7B%22hl%22%3A%7B%22description%22%3A%7B%22p%22%3A%22%3Cb%3E%22%2C%22s%22%3A%22%3C%5C%2Fb%3E%22%7D%7D%7D&p_sm=%7B%22msrp%22%3A%22asc%22%7D&p_lang=en&p_s=1&p_c=4&p_dr=line&p_aq=query%28%22Ford%22%29+OR+query%28scale%5E2%3A%221%3A21%22%29+AND+query%28line%3A%22Classic+Cars%22%29+AND+filter%28msrp%3E20%29+AND+filter%28vendor%3E%22bubacar%22%29+OR+filter%28msrp%3C%3D40%29"' + PHP_EOL;

       $search->query('Ford')
           ->orQuery('scale', '1:21', 2)
           ->andQueryByLine('Classic Cars')
           ->andFilterByMsrpGT(20)
           ->filter('vendor', 'bubacar', '>')
           ->orFilter('msrp', 40, '<=')
           ->prefixSuffix('description', '<b>', '</b>')
           ->sort(array('msrp' => 'asc'))
           ->lang('en')
           ->start(1)
           ->limit(4)
           ->duplicateRemoval('line')
           ->dump();

       $this->expectOutputString($expectedResult);
   }

    /**
     * @covers Cxsearch\Search::__call
     * @expectedException Cxsearch\UnknownMethod
     */
    public function testAttrUnknown()
    {
        $search = new Search($this->index);
        $search->asdaASaddadsadsa('cry');
    }

    /**
     * @covers Cxsearch\Search\Match::__construct
     */
    public function testSearchAllIndex()
    {
        $index = $this->getCxIndex(TRUE, '_all');

        $index->newSearch()->query('ford')->run($foo);
    }

    public function testAddFacetGroup()
    {
        $facetQuery = '{"vendor":{"d":"all"},"scale":{"d":10},"line":{"d":100}}';
        $expectedFacetQuery = '?p_aq=query("Ford")&p_f=' . $facetQuery;
        $search = new Search($this->index);

        $facetGroupMock = $this->getMock('Cxsearch\FacetGroup\FacetGroup');
        $facetGroupMock->expects($this->any())
                       ->method("buildQuery")
                       ->will($this->returnValue($facetQuery));

        $search->query('Ford')
            ->addFacetGroup($facetGroupMock);

        $search->dump($result);

        $result = urldecode($result);

        $this->assertEquals(
            $expectedFacetQuery,
            $result,
            "Result and expecting strings does not match"
        );
    }

    public function testReturnFields()
    {
        $retunFields = array ('vendor','line');
        $returnFieldsQuery = 'p_rs={"hl":{"description":{"p":"<b>","s":"<\/b>"}},"fl":["vendor","line"]}';
        $expectedFieldsQuery = '?p_aq=query("Ford")&' . $returnFieldsQuery;
        $search = new Search($this->index);

        $search->query('Ford')
               ->prefixSuffix('description', '<b>', '</b>')
               ->returnFields($retunFields);

        $search->dump($result);
        $result = urldecode($result);

        $this->assertEquals(
            $result,
            $expectedFieldsQuery,
            "Result and expecting strings does not match"
        );
    }

    /*
     * Test to assert Search::buildQuery joins FacetGroup::buildQuery as expected.
     */
    public function testAcceptSearchQuery()
    {
        $actualResult = "";
        $expectedResult = '?p_aq=query("Ford")&p_f={"line":{"d":"200","c":"5","lf":"1","r":[{"f":0,"t":100},{"f":120,"t":140}]},"msrp":{"d":"100","c":"10","lf":"1","r":[{"f":0,"t":150},{"f":200,"t":250}]}}';

        $search = new Search($this->index);
        $search->query('Ford')
            ->addFacetGroup(
                array(
                    array(
                        'fieldName' => 'line',
                        'depth' => '200',
                        'minCount' => '1',
                        'maxLabels' => '5',
                        'ranges' => array(
                            array(
                                'from' => 0,
                                'to' => 100
                            ),
                            array(
                                'from' => 120,
                                'to' => 140
                            )
                        )
                    ),
                    array(
                        'fieldName' => 'msrp',
                        'depth' => '100',
                        'minCount' => '1',
                        'maxLabels' => '10',
                        'ranges' => array(
                            array(
                                'from' => 0,
                                'to' => 150
                            ),
                            array(
                                'from' => 200,
                                'to' => 250
                            )
                        )
                    )
                )
            );
        $search->dump($actualResult);
        $actualResult = urldecode($actualResult);

        $this->assertEquals(
            $expectedResult,
            $actualResult
        );
    }

    public function testFilterArrayValue()
    {
        // arrange
        $expected = '?p_aq=filter(category_ids:[1,2,3])';
        $actual = '';

        // act
        $search = new Search($this->index);
        $search->filter('category_ids', array(1,2,3));
        $search->dump($actual);
        $actual = urldecode($actual);

        // assert
        $this->assertEquals($expected, $actual, 'Search should support array values in filters');
    }

    public function testFilterRange()
    {
        // arrange
        $expected = '?p_aq=filter(longitude:range(-123,-121.2))';
        $actual = '';

        // act
        $search = new Search($this->index);
        $search->filterRange('longitude', array(-123.0, -121.2));
        $search->dump($actual);
        $actual = urldecode($actual);

        // assert
        $this->assertEquals($expected, $actual, 'Search should support filter ranges');
    }

    public function testAndNotFilter()
    {
        // arrange
        $expected = '?p_aq=query("test") AND filter(rating:1) AND NOT filter(id:[1,2])';
        $actual = '';

        // act
        $search = new Search($this->index);
        $search->query("test");
        $search->filter("rating",1);
        $search->andNotFilter("id",array(1,2));
        $search->dump($actual);
        $actual = urldecode($actual);

        // assert
        $this->assertEquals($expected, $actual, 'Search should OR operator');
    }
}
