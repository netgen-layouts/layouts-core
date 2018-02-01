<?php

namespace Netgen\BlockManager\Core\Values\Collection;

use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\ValueObject;

final class Item extends ValueObject implements APIItem
{
    use ConfigAwareValueTrait;

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
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var int
     */
    protected $type;

    /**
     * @var int|string
     */
    protected $value;

    /**
     * @var string
     */
    protected $valueType;

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValueType()
    {
        return $this->valueType;
    }
}
