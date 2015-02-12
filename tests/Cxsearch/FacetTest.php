<?php

namespace Cxsearch;

use Cxsearch\FacetGroup\Facet;

class FacetTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new Facet();
    }

    /**
     */
    public function testMinCount()
    {
        $expected = 1;
        $this->object->setMinCount( $expected );
        $actual = $this->object->getMinCount();

        $this->assertEquals($expected, $actual, 'Mincount is not set properly');
    }

    /**
     */
    public function testDepth()
    {
        $expected = 1;
        $this->object->setDepth( $expected );
        $actual = $this->object->getDepth();

        $this->assertEquals($expected, $actual, 'Depth is not set properly');
    }

    /**
     */
    public function testRanges()
    {
        $expected = array(
            'from'  => 0,
            'to'    => 100,
        );
        $this->object->setRanges( $expected );
        $actual = $this->object->getRanges();

        $this->assertEquals($expected, $actual, 'Ranges is not set properly');
    }

    /**
     */
    public function testMaxLabels()
    {
        $expected = 1;
        $this->object->setMaxLabels( $expected );
        $actual = $this->object->getMaxLabels();

        $this->assertEquals($expected, $actual, 'Maxlabels is not set properly');
    }

    /**
     * @covers Cxsearch\Facet::buildQuery
     * @return JSON object
     */
    public function testBuildQuery()
    {
        $query = $this->object->buildQuery();
        print_r($query);
        json_decode($query);

        $this->assertTrue(json_last_error() == JSON_ERROR_NONE, 'Query is not in correct json format');
    }
}