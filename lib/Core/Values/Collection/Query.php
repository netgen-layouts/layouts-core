<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\Core\Values\ParameterBasedValueTrait;
use Netgen\BlockManager\ValueObject;

class Query extends ValueObject implements APIQuery
{
    use ParameterBasedValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int|string
     */
    protected $collectionId;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    protected $queryType;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    protected $parameters = array();

    /**
     * Returns the query ID.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the query status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the collection ID the query is in.
     *
     * @return int|string
     */
    public function getCollectionId()
    {
        return $this->collectionId;
    }

    /**
     * Returns if the query is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Returns the position the query is at.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
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
}
