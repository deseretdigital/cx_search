<?php

namespace Cxsearch\Search;

use Cxsearch\Document;

/**
* 
*/
class Match
{
    private $index;
    private $match;

    public $score;
    public $highlights;
    public $doc;
    public $sort;

    function __construct($index, $match)
    {
        $this->index = $index;
        $this->match = $match;

        $this->doc = Document::materialize($index, $match->document);

        $this->score = $match->score;
        $this->sort = $match->sortValues;
        $this->highlights = isset($match->highlights) ? $match->highlights : null;
    }
}