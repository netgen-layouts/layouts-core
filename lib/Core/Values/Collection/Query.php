<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Query as APIQuery;
use Netgen\BlockManager\API\Values\AbstractValue;

class Query extends AbstractValue implements APIQuery
{
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
     * @var int
     */
    protected $position;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the query parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns specified query parameter.
     *
     * @param string $parameter
     *
     * @return mixed
     */
    public function getParameter($parameter)
    {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : null;
    }

    /**
     * Returns if query has a specified parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter)
    {
        return isset($this->parameters[$parameter]);
    }
}
