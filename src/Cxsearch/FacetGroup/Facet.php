<?php
namespace Cxsearch\FacetGroup;

class Facet
{
    protected $fieldName;
    protected $minCount = 1;
    protected $depth = 'all';
    protected $ranges = null;
    protected $maxLabels = 100;

    public function __construct($data)
    {
        $this->setData($data);
    }
    /**
     * General purpose set data that will map
     * a data array to corresponding setter method
     *
     * Supported format for $data:
     * <pre>
     * array(
     *     {property} => {value}
     * )
     * </pre>
     *
     * @param array $data [description]
     * @return object
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $setter = 'set' . $key;
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                }
            }
        }
        return $this;
    }

    /**
     * Converts model into Array
     * @return array
     */
    public function toArray()
    {
        $properties = get_object_vars($this);
        $data = array();
        foreach ($properties as $key => $value) {
            $getter = 'get' . $key;
            if (method_exists($this, $getter)) {
                $data[$key] = $this->$getter();
            }
        }
        return $data;
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function setMinCount($minCount)
    {
        $this->minCount = $minCount;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public function setRanges($ranges)
    {
        $this->ranges = $ranges;
    }

    public function setMaxLabels($maxLabels)
    {
        $this->maxLabels = $maxLabels;
    }


    public function getFieldName()
    {
        return $this->fieldName;
    }

    /*
     * d - Depth for a shallow facet
     * @return int|string
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /*
     * c - Max number of facet labels to display.
     * @return int
     */
    public function getMinCount()
    {
        return $this->minCount;
    }

    /*
     * r - Range buckets (for numeric or date facets).
     * @return array
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /*
     * lf - Minimum frequency for a facet label for it to be included.
     * @return int
     */
    public function getMaxLabels()
    {
        return $this->maxLabels;
    }

    private function _addQuery($key, $value)
    {
        if ( !is_null($value) ) {
            $this->query[$key] = $value;
        }
    }

    /*
     * Build query for search
     * @return string
     */
    public function buildQuery()
    {
        $fieldName = $this->getFieldName();

        $query = array();
        $query['d'] = $this->getDepth();
        $query['c'] = $this->getMaxLabels();
        $query['lf'] = $this->getMinCount();

        $ranges = $this->getRanges();
        if (!empty($ranges)) {
            $query['r'] = $ranges;
        }

        return json_encode(array($fieldName => $query));
    }
}
