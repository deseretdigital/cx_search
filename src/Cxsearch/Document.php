<?php

namespace Cxsearch;

use Cxsearch\Index;
use Buzz\Browser;

class Document {
	private $_index;
	private $_baseUrl;

	private $_is_new = FALSE;

	private $_data;
	private $_new_data;

	public static function load(Index $index, $id)
	{
		$browser = new Browser();
        $response = $browser->get($index->getBaseUrl() . '/api/content/' . $index->getId() . '/' . $id);
        $doc = json_decode($response->getContent());

		$obj = new Document($index, $doc);
		return $obj;
	}

	public static function create(Index $index, $id)
	{
		$obj = new Document($index);
		$obj->id = $id;
		return $obj;
	}

	private function __construct(Index $index, $data=null)
	{
		$this->_index = $index;
		$this->_baseUrl = $index->getBaseUrl();
		$this->_new_data = new \stdClass;

		if (is_null($data)) {
			$this->_is_new = TRUE;
			$this->skeleton();
			return;
		}

		$this->_data = $data;
	}

	private function skeleton()
	{
		$def = $this->_index->getDef();

		foreach ($def->configuration->fields as $field_name => $field_info)
		{
			$this->{$field_name} = null;
		}
	}

	public function __isset($name)
	{
		if ($name == 'id' || $name == 'version') {
			return isset($this->_data->{$name}) || isset($this->_new_data->{$name});
		}

		return isset($this->_data->fields->{$name}) || isset($this->_new_data->fields->{$name});
	}

	public function __set($name, $value)
	{
		if ($name == 'id') {
			if (!$this->_is_new) {
				return FALSE;
			}
			$this->_new_data->id = $value;
			return;
		}

		if (!isset($this->_new_data->fields)) {
			$this->_new_data->fields = new \stdClass;
		}

		$this->_new_data->fields->{$name} = $value;
	}

	public function __get($name)
	{
		if (!isset($this->{$name})) {
			$trace = debug_backtrace();
	        trigger_error(
	            'Undefined property via __get(): ' . $name .
	            ' in ' . $trace[0]['file'] .
	            ' on line ' . $trace[0]['line'],
	            E_USER_NOTICE);
	        return null;
		}

		if ($name == 'id' || $name == 'version') {
			return isset($this->_new_data->{$name}) ? $this->_new_data->{$name} : $this->_data->{$name};
		}

		return isset($this->_new_data->fields->{$name}) ? $this->_new_data->fields->{$name} : $this->_data->fields->{$name};
	}

	private function buildUrl()
    {
        return $this->_baseUrl . '/api/content/'. $this->_index->getId();
    }

    public function getData()
    {
    	return $this->_data;
    }

    public function save() {
    	// Nothing to do!
    	if (is_null($this->_new_data)) {
    		return;
    	}

    	$browser = new Browser();
    	$url = $this->buildUrl() . "/" . $this->id;

    	if ($this->_is_new) {
    		$data = json_encode($this->_new_data->fields);
        	$response = $browser->post($url, array(), $data);
        	return $response->isSuccessful();
    	}

    	$data = (object) array_merge((array) $this->_data->fields, (array) $this->_new_data->fields);

    	$this->delete();

    	$response = $browser->post($url, array(), $data);
        return $response->isSuccessful();
    }

    public function delete()
    {
    	// Nothing to do!
    	if ($this->_is_new) {
    		return;
    	}

    	$browser = new Browser();
    	$url = $this->buildUrl() . "/" . $this->id;
    	$response = $browser->delete($url);

        return $response->isSuccessful();
    }
}