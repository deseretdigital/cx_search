<?php

namespace Cxsearch;

use Cxsearch\FacetGroup\FacetGroup;
use Cxsearch\Search\Result;

class Search
{
    private $_index;
    private $_qry = array();
    private $_a_qry = array();
    private $_f_qry = "";

    public function __construct($index)
    {
        $this->_index = $index;
        return $this;
    }

    private function _addQuery($key, $data)
    {
        $this->_qry[$key] = $data;
    }

    // Normal Search

    public function lang($lang)
    {
        $this->_addQuery('p_lang', $lang);
        return $this;
    }

    public function sort($sort)
    {
        $this->_addQuery('p_sm', json_encode($sort));
        return $this;
    }

    public function start($start)
    {
        $this->_addQuery('p_s', $start);
        return $this;
    }

    public function limit($limit)
    {
        $this->_addQuery('p_c', $limit);
        return $this;
    }

    public function duplicateRemoval($fields)
    {
        $this->_addQuery('p_dr', $fields);
        return $this;
    }

    /**
     * @param array | FacetGroup $fields
     * @return $this
     */
    public function addFacetGroup($fields)
    {
        if (is_array($fields)) {
            $facetGroup = new FacetGroup();
            foreach($fields as $field) {
                $facetGroup->newFacet($field);
            }
        } else {
            $facetGroup = $fields;
        }

        $this->_f_qry = $facetGroup;

        return $this;
    }

    public function prefixSuffix($target, $prefix, $suffix)
    {
        $rs = array(
            'hl' => array(
                $target => array(
                    'p' => $prefix,
                    's' => $suffix
                )
            )
        );
        $this->_addQuery('p_rs', json_encode($rs));
        return $this;
    }

    // Advanced Search

    private function _prefix($cmd, $prefix=null)
    {
        $prefix = is_null($prefix) ? 'AND' : $prefix;
        $prefix = count($this->_a_qry) == 0 ? '' : strtoupper($prefix) . ' ';
        return trim($prefix . $cmd);
    }

    private function _query($target, $value=null, $boost=null, $prefix='AND')
    {
        if (is_null($value)) {
            $value = $target;
            $target = null;
        } else if (!is_null($boost)) {
            $target .= '^'.$boost;
        }

        if (!is_null($target)) {
            $value = $target .':"'. $value .'"';
        } else {
            $value = '"' . $value . '"';
        }

        $this->_a_qry[] = $this->_prefix("query({$value})", $prefix);
    }

    public function query($target, $value=null, $boost=null, $prefix=null)
    {
        $this->_query($target, $value, $boost, $prefix);
        return $this;
    }

    public function orQuery($target, $value=null, $boost=null)
    {
        $this->_query($target, $value, $boost, 'OR');
        return $this;
    }

    private function _filter($target, $operator, $value, $prefix=null)
    {

        if (!is_numeric($value)&&!is_array($value)) {
            $value = '"' . $value . '"';
        } else if(is_array($value)){
            $value = 'range('.implode(',', $value).')';
        }

        $this->_a_qry[] = $this->_prefix("filter({$target}{$operator}{$value})", $prefix);
    }

    public function filter($target, $value, $op=':', $prefix=null)
    {
        $this->_filter($target, $op, $value, $prefix);
        return $this;
    }

    public function orFilter($target, $value, $op=':')
    {
        $this->_filter($target, $op, $value, 'OR');
        return $this;
    }

    // Auto Queries/Fields

    private function _callQuery($params, $args)
    {
        $value = array_shift($args);
        $boost = count($args) ? array_shift($args) : null;

        $prefix = empty($params[1]) ? null : strtoupper($params[1]);
        $target = strtolower($params[4]);

        $this->_query($target, $value, $boost, $prefix);
    }

    private function _callFilter($params, $args)
    {
        $value = array_shift($args);
        $prefix = empty($params[1]) ? null : strtoupper($params[1]);
        $target = strtolower($params[4]);

        $_ops = array(
            'LT'  => '<',
            'LTE' => '<=',
            'GT'  => '>',
            'GTE' => '>='
        );

        $op = isset($params[5]) ? $_ops[$params[5]] : ':';

        $this->_filter($target, $op, $value, $prefix);
    }

    public function __call($name, $args)
    {
        // queryByTarget ( value, [boost] )
        // [and|or]QueryByTarget ( value, [boost] )
        if (preg_match('/(?:(and|or)(Query)|^(query))By(.*?)$/', $name, $rs)) {
            $this->_callQuery($rs, $args);
        } else if (preg_match('/(?:(and|or)(Filter)|^(filter))By(.*?)([GL]TE?)?$/', $name, $rs)) {
            $this->_callFilter($rs, $args);
        } else {
            $trace = debug_backtrace();
            throw new UnknownMethod('Undefined method via __call(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line']);
        }

        return $this;
    }

    private function _buildQuery($encode=TRUE)
    {
        // Add Advanced Query
        $this->_addQuery('p_aq', join($this->_a_qry, ' '));

        if (!empty($this->_f_qry)) {
            $this->_addQuery('p_f', $this->_f_qry->buildQuery());
        }

        $final = array();
        foreach ($this->_qry as $key => $value) {
            $final[$key] = $value;
        }

        $queryString = '?' . http_build_query($final);

        if (!$encode) {
            $queryString = urldecode($queryString);
        }

        return $queryString;
    }

    public function dump(&$result=FALSE)
    {
        $query = $this->_buildQuery(FALSE);

        if ($result === FALSE) {
            var_dump($query);
            return;
        }

        $result = $query;
    }

    public function run(&$result)
    {
        $query = $this->_buildQuery();

        $browser = $this->_index->getBrowser();
        $url = $this->_index->getBaseUrl() . '/api/search/' . $this->_index->getId() . '/' . $query;

        $response = $browser->get($url);

        if (!$response->isSuccessful()) {
            $result = $response->getContent();
            return FALSE;
        }
        $data = json_decode($response->getContent());
        $result = new Result($this->_index, $data);
    }
}

class UnknownMethod extends \Exception {}
