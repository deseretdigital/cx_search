<?php
namespace Cxsearch;

use Cxsearch\Search\Result;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testFacetReturned()
    {
        // arrange
        $expected = 'arbitrary_value';
        $indexMock = new \stdClass();
        $resultset = new \stdClass();
        $resultset->facets = $expected;
        $resultset->start = 0;
        $resultset->totalMatched = 10;
        $resultset->matches = array();
        $resultObj = new Result($indexMock, $resultset);

        // act
        $actual = $resultObj->getFacets();

        // assert
        $this->assertEquals($expected, $actual, 'Result object did not return expected value.');
    }
}
