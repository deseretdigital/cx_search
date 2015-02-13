<?php
namespace Cxsearch\FacetGroup;

class Facet
{
    private $field;
    private $minCount;
    private $depth;
    private $ranges;
    private $maxLabels;
    private $query;

    public function __construct($fieldName, $fieldParams)
    {
        $this->field = $fieldName;
        $this->setDepth($fieldParams['documentCount']);
        $this->setMinCount($fieldParams['count']);
        $this->setRanges($fieldParams['range']);
        $this->setMaxLabels($fieldParams['leastFrequency']);
    }

    public function setMinCount($minCount)
    {
        $this->minCount = $minCount;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public function setRanges($ranges)
    {
        $this->ranges = $ranges;
    }

    public function setMaxLabels($maxLabels)
    {
        $this->maxLabels = $maxLabels;
    }

    /*
     * d - Depth for a shallow facet
     * @return int|string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /*
     * c - Max number of facet labels to display.
     * @return int
     */
    public function getMinCount()
    {
        return $this->minCount;
    }

    /*
     * r - Range buckets (for numeric or date facets).
     * @return array
     */
    public function getRanges()
    {
        return array(
            'from'  => $this->ranges[0],
            'to'    => $this->ranges[1]
        );
    }

    /*
     * lf - Minimum frequency for a facet label for it to be included.
     * @return int
     */
    public function getMaxLabels()
    {
        return $this->maxLabels;
    }

    private function _addQuery($key, $value)
    {
        $this->query[$key] = $value;
    }

    private function depth()
    {
        $this->_addQuery('d', $this->getDepth());
        return $this;
    }

    private function maxLabels()
    {
        $this->_addQuery('c', $this->getMaxLabels());
        return $this;
    }

    private function minCount()
    {
        $this->_addQuery('lf', $this->getMinCount());
        return $this;
    }

    private function ranges()
    {
        $this->_addQuery('r', json_encode($this->getRanges()));
        return $this;
    }

    private function buildJson()
    {
        $json = array();

        foreach( $this->query as $key=>$value ) {
            $json[] = $key . ':' . $value;
        }
        $json = $this->field . json_encode( implode('', $json) );

        return json_encode($json);
    }

    /*
     * Build query for search
     * @return string
     */
    public function buildQuery()
    {
        return $this->depth()
                    ->maxLabels()
                    ->minCount()
                    ->ranges()
                    ->buildJson();
    }
}