<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Query extends Value, ParameterBasedValue
{
    /**
     * Returns the query ID.
     *
     * @return int|string
     */
    public function getId();

    /**
     * Returns the query status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns the collection ID the query is in.
     *
     * @return int|string
     */
    public function getCollectionId();

    /**
     * Returns if the query is published.
     *
     * @return bool
     */
    public function isPublished();

    /**
     * Returns the position the query is at.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Returns the query identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the query type.
     *
     * @return \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    public function getQueryType();
}
