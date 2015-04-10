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

    /**
     * Serialize Result object
     * @return array Returns array representation of Result object
     */
    public function toArray()
    {
        $matches = array_map(function($match) {
            return $match->toArray();
        }, $this->matches);

        return array(
            'matches' => $matches,
            'start' => $this->getStart(),
            'totalMatched'=> $this->length(),
            'annotations' => json_decode(json_encode($this->raw->annotations), true),
            'facets' => json_decode(json_encode($this->getFacets()), true)
        );
    }
}
