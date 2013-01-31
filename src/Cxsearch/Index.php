<?php

namespace Cxsearch;

use Buzz\Browser;

class Index {
    private $baseUrl;
    private $id;
    private $browser;

    public function __construct($id, $browser = null)
    {
        $this->id = $id;
        if (is_null($browser)) {
            $this->setBrowser(new Browser());
        } else {
            $this->setBrowser($browser);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    private function buildUrl()
    {
        return $this->baseUrl . '/api/index/'. $this->id;
    }

    public function getDef($reset=FALSE)
    {
        static $data;

        if (is_null($data) || $reset) {
            $response = $this->browser->get($this->buildUrl());
            $data = json_decode($response->getContent());
        }

        return $data;
    }

    public function getDocument($id)
    {
        return Document::load($this, $id);
    }

    public function newDocument($id)
    {
        return Document::create($this, $id);
    }

    public function newSearch()
    {
        return new Search($this);
    }
}