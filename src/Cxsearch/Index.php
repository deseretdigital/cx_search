<?php

namespace Cxsearch;

use Buzz\Browser;

class Index {
    private $baseUrl;
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
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

    private function buildUrl()
    {
        return $this->baseUrl . '/api/index/'. $this->id;
    }

    public function getDef()
    {
        static $data;

        if (is_null($data)) {
            $browser = new Browser();
            $response = $browser->get($this->buildUrl());
            $data = json_decode($response->getContent());
        }

        return $data;
    }

    public function getDocument($id)
    {
        return Document::load($this, $id);
    }
}