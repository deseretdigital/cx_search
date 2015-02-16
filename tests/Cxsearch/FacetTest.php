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

    public function testSetData()
    {
        // arrange
        $expected = array(
            'FieldName' => 'line',
            'Depth'     => '200',
            'MinCount'  => '1',
            'MaxLabels' => '5',
            'Ranges'    => array(0, 100)
            );
        // act
        $this->object->setData($expected);
        $actual = $this->object->toArray();

        // assert
        $this->assertEquals($expected, $actual, 'Data not set properly');
    }

    /**
     * @covers Cxsearch\Facet::buildQuery
     * @return JSON object
     */
    public function testBuildQuery()
    {
        $expected = array(
            'FieldName' => 'line',
            'Depth'     => '200',
            'MinCount'  => '1',
            'MaxLabels' => '5',
            'Ranges'    => array(0, 100)
        );
        $this->object->setData($expected);

        $query = $this->object->buildQuery();
        json_decode($query);
        $this->assertFalse(json_last_error() == JSON_ERROR_NONE, 'Query is not in correct json format');
    }
}