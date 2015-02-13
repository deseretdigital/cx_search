<?php

namespace Cxsearch;

use Cxsearch\FacetGroup\Facet;
use Cxsearch\FacetGroup\FacetGroup;

class FacetGroupTest extends \PHPUnit_Framework_TestCase
{
    protected $facetGroup;

    protected function setUp()
    {
        $this->facetGroup = new FacetGroup();
        $this->facetGroup->newFacet("line",
            array(
                'documentCount' => 200,
                'count'         => 100,
                'range'         => array(0, 100),
                'leastFrequency'=> 1
            )
        );
        $this->facetGroup->newFacet("msrp",
            array(
                'documentCount' => 200,
                'count'         => 100,
                'range'         => array(100, 200),
                'leastFrequency'=> 1
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
        $query = $this->facetGroup->buildQuery();
        json_decode($query);
        echo json_decode($query);

        $this->assertFalse(json_last_error() == JSON_ERROR_NONE, 'Group query is not in correct json format');

    }
}