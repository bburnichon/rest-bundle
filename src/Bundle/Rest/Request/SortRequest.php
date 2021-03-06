<?php

namespace Alchemy\RestBundle\Rest\Request;

use Alchemy\Rest\Request\Sort;
use Alchemy\Rest\Request\SortOptions;

class SortRequest implements SortOptions
{
    /**
     * @var string|array
     */
    private $sorts;

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $direction;


    public function __construct($sorts, $property = null, $direction = null)
    {
        $this->sorts = $sorts;
        $this->property = $property;
        $this->direction = $direction;
    }

    /**
     * @param array $sortMap
     * @return array
     */
    public function getSorts(array $sortMap = array())
    {
        if ($this->sorts === null && $this->property !== null && $this->direction !== null) {
            $this->sorts = array(array($this->property, $this->direction));
        }

        if ($this->sorts === null) {
            return array();
        }

        if (is_string($this->sorts)) {
            $this->sorts = explode(',', $this->sorts);
        }

        $sorts = array();

        foreach ($this->sorts as $sort) {
            $sort = $this->normalizeSort($sort);
            $sort = $this->mapSortProperty($sortMap, $sort[0], $sort[1]);

            if ($sort) {
                $sorts[] = $sort;
            }
        }

        return $sorts;
    }

    /**
     * @param $sort
     * @return array
     */
    private function normalizeSort($sort)
    {
        if (is_string($sort)) {
            $sort = explode(':', $sort);
        }

        if (count($sort) < 2) {
            $sort[1] = self::SORT_ASC;

            return $sort;
        }

        return $sort;
    }

    /**
     * @param array $sortMap
     * @param $sort
     * @param $direction
     * @return Sort
     */
    private function mapSortProperty(array $sortMap, $sort, $direction)
    {
        if (isset($sortMap[$sort])) {
            $sort = $sortMap[$sort];
        }

        return new Sort($sort, $direction);
    }
}
