<?php

namespace Cxsearch;

use Buzz\Browser;

/**
* 
*/
class SearchTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->baseUrl = 'http://sandbox.cxsearch.cxense.com';
        $this->index = new Index('birt');
        $this->index->setBaseUrl($this->baseUrl);
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
        $search = $this->index->newSearch();
        $this->assertInstanceOf('Cxsearch\Search', $search);

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

        $this->assertEquals($result, '?p_rs={"hl":{"description":{"p":"<b>","s":"<\/b>"}}}&p_sm={"msrp":"asc"}&p_lang=en&p_s=1&p_c=4&p_dr=line&p_aq=query("Ford") OR query(scale^2:"1:21") AND query(line:"Classic Cars") AND filter(msrp>20) AND filter(vendor>"bubacar") OR filter(msrp<=40)');
    }

    /**
     * @covers Cxsearch\Search::_buildQuery
     * @covers Cxsearch\Search::run
     * @covers Cxsearch\Search\Result::__construct
     * @covers Cxsearch\Search\Match::__construct
     */
    public function testRunSearch()
    {
        $search = $this->index->newSearch()
            ->query('Ford')
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
}