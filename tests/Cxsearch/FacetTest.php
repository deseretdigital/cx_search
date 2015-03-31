<?php

namespace Cxsearch;

use Cxsearch\FacetGroup\Facet;

class FacetTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    public function testSetData()
    {
        $expected = array(
            'fieldName' => 'line',
            'depth'     => '200',
            'minCount'  => '1',
            'maxLabels' => '5',
            'ranges'    => array(
                    array(
                        'from'  => 0,
                        'to'    => 100
                    ),
                    array(
                        'from'  => 120,
                        'to'    => 140
                    )
                )
            );
        $this->object = new Facet($expected);
        $actual = $this->object->toArray();

        $this->assertEquals($expected, $actual, 'Data not set properly');
    }

    /**
     * @covers Cxsearch\Facet::buildQuery
     * @return JSON object
     */
    public function testBuildQuery()
    {
        $expected = '{"line":{"d":"200","c":"5","lf":"1","r":[{"f":0,"t":100},{"f":120,"t":140}]}}';
        $data = array(
            'fieldName' => 'line',
            'depth'     => '200',
            'minCount'  => '1',
            'maxLabels' => '5',
            'ranges'    => array(
                array(
                    'from'  => 0,
                    'to'    => 100
                ),
                array(
                    'from'  => 120,
                    'to'    => 140
                )
            )
        );
        $this->object = new Facet($data);

        $this->assertJsonStringEqualsJsonString(
            $expected, $this->object->buildQuery(), 'Query is not in expected json format'
        );
    }


    public function testFacetDefaults()
    {
        // arrange
        $data = array(
            'fieldName' => 'star_rating'
        );
        $expected = '{"star_rating":{"d":"all","c":100,"lf":1}}';

        // act
        $this->object = new Facet($data);

        // assert
        $this->assertJsonStringEqualsJsonString(
            $expected, $this->object->buildQuery(), 'Query is not in expected json format'
        );
    }

    public function testConstructRanges()
    {
        // arrange
        $data = array(
            'fieldName' => 'star_rating',
            'ranges' => array(
                array('from' => 1, 'to' => 6),
                array('from' => 2, 'to' => 6),
                array('from' => 3, 'to' => 6),
                array('from' => 4, 'to' => 6),
                array('from' => 5, 'to' => 6),
            )
        );

        $expected = '{"star_rating":{"d":"all","c":100,"lf":1,"r":[{"f":1,"t":6},{"f":2,"t":6},{"f":3,"t":6},{"f":4,"t":6},{"f":5,"t":6}]}}';

        // act
        $this->object = new Facet($data);

        // assert
        $this->assertJsonStringEqualsJsonString(
            $expected, $this->object->buildQuery(), 'Query is not in expected json format'
        );
    }
}
