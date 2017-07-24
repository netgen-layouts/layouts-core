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
     * @var \Netgen\BlockManager\Collection\QueryTypeInterface
     */
    protected $queryType;

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
     * Returns the status of the value.
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
     * Returns the limit internal to the query.
     *
     * @return int
     */
    public function getInternalLimit()
    {
        return $this->queryType->getInternalLimit($this);
    }

    /**
     * Returns if the query is dependent on a context, i.e. current request.
     *
     * @return bool
     */
    public function isContextual()
    {
        return $this->queryType->isContextual($this);
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
