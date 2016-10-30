<?php

namespace Netgen\BlockManager\API\Values\Collection;

use Netgen\BlockManager\API\Values\Value;

interface Query extends Value
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

    /**
     * Returns the query parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    public function getParameters();

    /**
     * Returns specified query parameter.
     *
     * @param string $parameter
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameter);

    /**
     * Returns if query has a specified parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter);
}
