<?php

namespace Cxsearch\Search;

use Cxsearch\Document;

/**
*
*/
class Result implements \Iterator
{
    private $position;
    private $index;
    private $raw;
    private $matches = array();

    private $start;
    private $totalMatched;

    public function __construct($index, $resultset)
    {
        $this->position     = 0;
        $this->index        = $index;
        $this->raw          = $resultset;
        $this->start        = $resultset->start;
        $this->totalMatched = $resultset->totalMatched;
        foreach ($resultset->matches as $match) {
            $this->matches[] = new Match($this->index, $match);
        }
    }

    public function getStart()
    {
        return $this->start;
    }

    public function length()
    {
        return $this->totalMatched;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->matches[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->matches[$this->position]);
    }

    public function getFacets()
    {
        return $this->raw->facets;
    }

    public function getFacetByName($facetName)
    {
        if (!isset($this->raw->facets->{$facetName})) {
            return false;
        };
        $values = $this->raw->facets->{$facetName};
        foreach ($values as $value) {
            $result[] = get_object_vars($value);
        }

        return $result;
    }
}
