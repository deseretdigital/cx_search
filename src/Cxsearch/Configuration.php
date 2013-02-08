<?php

namespace Cxsearch;

use Buzz\Browser;

class Configuration
{
    public $username;
    public $password;
    public $api_url = 'http://sandbox.cxsearch.cxense.com';
    public $browser;

    public function __construct()
    {
        $this->browser = new Browser;
    }

    public function setBaseUrl($url)
    {
        $this->api_url = $url;
    }

    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
    }
}