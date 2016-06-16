<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;

class QueryType implements QueryTypeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param string $type
     * @param \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface $handler
     * @param \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration $config
     */
    public function __construct($type, QueryTypeHandlerInterface $handler, Configuration $config)
    {
        $this->type = $type;
        $this->handler = $handler;
        $this->config = $config;
    }

    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
