<?php

namespace Cxsearch;

use Buzz\Browser;

class Index {
    private $baseUrl;
    private $index;

    public function __construct($index)
    {
        $this->index = $index;
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    private function buildUrl()
    {
        return $this->baseUrl . '/api/index/'. $this->index;
    }

    public function loadDef()
    {
        $response = new Browser()->get($this->buildUrl());
    }
}