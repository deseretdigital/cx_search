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
        $expected = '{"line":{"d":"200","c":"5","lf":"1","r":[{"from":0,"to":100},{"from":120,"to":140}]}}';
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
}