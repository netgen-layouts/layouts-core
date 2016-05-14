<?php

namespace Netgen\BlockManager\Configuration\Source;

class Query
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
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
     * @param string $queryType
     * @param array $defaultParameters
     */
    public function __construct($identifier, $queryType, array $defaultParameters)
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
     * @return string
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * Returns the query default parameters.
     *
     * @return string
     */
    public function getDefaultParameters()
    {
        return $this->defaultParameters;
    }
}
