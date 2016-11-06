<?php

namespace Netgen\BlockManager\Configuration\Source;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\ValueObject;

class Source extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Configuration\Source\Query[]
     */
    protected $queries = array();

    /**
     * Returns the source identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the source name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the source queries.
     *
     * @return \Netgen\BlockManager\Configuration\Source\Query[]
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * Returns if the source has a query with provided identifier.
     *
     * @param $queryIdentifier
     *
     * @return bool
     */
    public function hasQuery($queryIdentifier)
    {
        return isset($this->queries[$queryIdentifier]);
    }

    /**
     * Returns the query with provided identifier.
     *
     * @param $queryIdentifier
     *
     * @throws \InvalidArgumentException If query does not exist
     *
     * @return \Netgen\BlockManager\Configuration\Source\Query
     */
    public function getQuery($queryIdentifier)
    {
        if (!$this->hasQuery($queryIdentifier)) {
            throw new InvalidArgumentException(
                'queryIdentifier',
                sprintf(
                    'Query with "%s" identifier does not exist in "%s" source.',
                    $queryIdentifier,
                    $this->identifier
                )
            );
        }

        return $this->queries[$queryIdentifier];
    }
}
