<?php
namespace Cxsearch\FacetGroup;

class FacetGroup
{
    private $facets = array();

    public function newFacet($fieldName, $fieldParams)
    {
        $this->facets[] = new Facet($fieldName, $fieldParams);
    }

    public function getFacets()
    {
        return $this->facets;
    }

    public function buildQuery()
    {
        $joinedJson = array();

        foreach( $this->facets as $facet ) {
            $joinedJson[] = $facet->buildQuery();
        }

        return json_encode(implode(',', $joinedJson));
    }
}