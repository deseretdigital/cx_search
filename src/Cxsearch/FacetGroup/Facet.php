<?php
namespace Cxsearch\FacetGroup;

class Facet
{
    private $minCount;
    private $depth;
    private $ranges;
    private $maxLabels;

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
     * lf - Minimum frequency for a facet label for it to be included.
     * @return int
     */
    public function getMinCount()
    {
        return $this->minCount;
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
     * r - Range buckets (for numeric or date facets).
     * @return array
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /*
     * c - Max number of facet labels to display.
     * @return int
     */
    public function getMaxLabels()
    {
        return $this->maxLabels;
    }

    /*
     * Build query for search
     * @return string
     */
    public function buildQuery()
    {
        $range = $this->getRanges();
        return '{"line": {"d": "'.$this->getDepth().'", {"c": "'.$this->getMaxLabels().'"},{"lf": "'
                .$this->getMinCount().'"},{"r":{"from":"'.$range['from'].'","to":"'.$range['to'].'"}}}';
    }
}