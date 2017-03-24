<?php

namespace Netgen\BlockManager\Collection\Source;

use Netgen\BlockManager\ValueObject;

class Query extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    protected $queryType;

    /**
     * @var array
     */
    protected $defaultParameters = array();

    /**
     * Returns the query identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the query type.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * Returns the query default parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->defaultParameters;
    }
}
