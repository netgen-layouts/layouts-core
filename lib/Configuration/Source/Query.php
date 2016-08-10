<?php

namespace Netgen\BlockManager\Configuration\Source;

use Netgen\BlockManager\Collection\QueryTypeInterface;

class Query
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
     * Constructor.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Collection\QueryTypeInterface $queryType
     * @param array $defaultParameters
     */
    public function __construct($identifier, QueryTypeInterface $queryType, array $defaultParameters)
    {
        $this->identifier = $identifier;
        $this->queryType = $queryType;
        $this->defaultParameters = $defaultParameters;
    }

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
