<?php

namespace Cxsearch\Search;

use Cxsearch\Document;
use Cxsearch\Index;

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

    public function __construct($index, $match)
    {
        $this->index = $index;
        $this->match = $match;

        if ($index->getId() != $match->index) {
            $this->index = new Index($index->getConfiguration(), $match->index);
        }

        $this->doc = Document::materialize($this->index, $match->document);

        $this->score = $match->score;
        $this->sort = $match->sortValues;
        $this->highlights = isset($match->highlights) ? $match->highlights : null;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function toArray()
    {

        $data = array(
            'index'      => $this->index->getId(),
            'document'   => $this->doc->toArray(),
            'score'      => $this->score,
            'sortValues' => $this->sort,
            'highlights' => $this->highlights
        );

        return $data;
    }
}
