<?php

namespace Cxsearch;

class Document
{
    private $_index;
    private $_baseUrl;
    private $_raw_data;

    private $_is_new = FALSE;
    private $_is_changed = FALSE;

    private $_data;
    private $_new_data;

    public static function load(Index $index, $id)
    {
        $browser = $index->getBrowser();
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

    public static function materialize(Index $index, $data)
    {
        $obj = new Document($index, $data);
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

    public function isNew()
    {
        return $this->_is_new;
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

        if (!$this->_is_changed && !is_null($value)) {
            $this->_is_changed = TRUE;
        }
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

    private function _saved()
    {
        $newDoc = self::load($this->_index, $this->id);

        $this->_data = $newDoc->getData();
        unset($newDoc);

        $this->_is_new = FALSE;
        $this->_is_changed = FALSE;
        //$this->_data->id = $this->_new_data->id;
        $this->_new_data = new \stdClass;
    }

    public function save()
    {
        // Nothing to do!
        if (!$this->_is_changed) {
            return;
        }

        $browser = $this->_index->getBrowser();
        $url = $this->buildUrl() . "/" . $this->id;

        if ($this->_is_new) {
            $data = json_encode(array('fields' => $this->_new_data->fields));
            $response = $browser->post($url, array(), $data);
            
            if ($response->isSuccessful()) {
                $this->_saved();
                return TRUE;
            }

            return FALSE;
        }

        $data = (object) array_merge((array) $this->_data->fields, (array) $this->_new_data->fields);
        $data = json_encode(array('fields' => $data));

        $response = $browser->post($url, array(), $data);
        
        if ($response->isSuccessful()) {
            $this->_saved();
            return TRUE;
        }

        return FALSE;
    }

    public function delete()
    {
        // Nothing to do!
        if ($this->_is_new) {
            return;
        }

        $browser = $this->_index->getBrowser();
        $url = $this->buildUrl() . "/" . $this->id;
        $response = $browser->delete($url);

        return $response->isSuccessful();
    }
}