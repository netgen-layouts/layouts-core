<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

abstract class Query extends Value
{
    /**
     * Returns the query ID.
     *
     * @return int|string
     */
    abstract public function getId();

    /**
     * Returns the query status.
     *
     * @return int
     */
    abstract public function getStatus();

    /**
     * Returns the collection ID the query is in.
     *
     * @return int|string
     */
    abstract public function getCollectionId();

    /**
     * Returns the query identifier.
     *
     * @return string
     */
    abstract public function getIdentifier();

    /**
     * Returns the query type.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Returns the query parameters.
     *
     * @return array
     */
    abstract public function getParameters();
}
