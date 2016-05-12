<?php

namespace Netgen\BlockManager\API\Values\Collection;

interface Query
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
     * @return string
     */
    public function getType();

    /**
     * Returns the query parameters.
     *
     * @return array
     */
    public function getParameters();
}
