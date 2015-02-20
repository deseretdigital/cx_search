<?php
namespace Cxsearch\FacetGroup;

class FacetGroup
{
    private $facets = array();

    public function newFacet($data)
    {
        $this->facets[] = new Facet($data);
    }

    public function getFacets()
    {
        return $this->facets;
    }

    public function buildQuery()
    {
        $joinedJson = array();

        foreach( $this->facets as $facet ) {
            foreach( json_decode($facet->buildQuery()) as $key=>$val ) {
                $joinedJson[$key] = $val;
            }
        }

        return json_encode($joinedJson);
    }
}