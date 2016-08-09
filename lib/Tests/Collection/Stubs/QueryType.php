<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryTypeInterface;

class QueryType implements QueryTypeInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $values;

    /**
     * Constructor.
     *
     * @param string $type
     * @param array $values
     */
    public function __construct($type, array $values = array())
    {
        $this->type = $type;
        $this->values = $values;
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
        return new QueryTypeHandler($this->values);
    }

    /**
     * Returns the query type configuration.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Configuration
     */
    public function getConfig()
    {
        return new Configuration($this->type, $this->type);
    }
}
