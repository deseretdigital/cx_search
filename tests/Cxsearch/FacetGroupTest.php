<?php

namespace Cxsearch;

use Cxsearch\FacetGroup\FacetGroup;

class FacetGroupTest extends \PHPUnit_Framework_TestCase
{
    protected $facetGroup;

    protected function setUp()
    {
        $this->facetGroup = new FacetGroup();
        $this->facetGroup->newFacet(array(
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
            )
        );
        $this->facetGroup->newFacet(
            array(
                'fieldName' => 'msrp',
                'depth'     => '100',
                'minCount'  => '1',
                'maxLabels' => '10',
                'ranges'    => array(
                    array(
                        'from'  => 0,
                        'to'    => 150
                    ),
                    array(
                        'from'  => 200,
                        'to'    => 250
                    )
                )
            )
        );
    }

    public function testNewFacet()
    {
        foreach( $this->facetGroup->getFacets() as $facet ) {
            $this->assertInstanceOf('Cxsearch\FacetGroup\Facet', $facet, 'Element is not an Facet instance');
        }
    }

    public function testBuildQuery()
    {
        $expected = '{"line":{"d":"200","c":"5","lf":"1","r":[{"f":0,"t":100},{"f":120,"t":140}]},'
                   .'"msrp":{"d":"100","c":"10","lf":"1","r":[{"f":0,"t":150},{"f":200,"t":250}]}}';

        $this->assertJsonStringEqualsJsonString(
            $expected, $this->facetGroup->buildQuery(), 'Group query is not in correct json format'
        );

    }
}
