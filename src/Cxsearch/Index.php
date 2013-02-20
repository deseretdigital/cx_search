<?php

namespace Cxsearch;

use Buzz\Browser;

class Index
{
    private $config;
    private $id;

    public function __construct(Configuration $config, $id=NULL)
    {
        $this->config = $config;

        if (!is_null($id)) {
            $this->setIndex($id);
        }
    }

    public function setIndex($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    public function getBaseUrl()
    {
        return $this->config->api_url;
    }

    public function getBrowser()
    {
        return $this->config->browser;
    }

    public function setBrowser(Browser $browser)
    {
        $this->config->setBrowser($browser);
    }

    private function buildUrl()
    {
        return $this->getBaseUrl() . '/api/index/'. $this->id;
    }

    public function getDef($reset=FALSE)
    {
        static $data;

        if (is_null($data) || $reset) {
            $broser = $this->config->browser;
            $response = $broser->get($this->buildUrl());
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