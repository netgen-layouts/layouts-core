<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use DateTimeInterface;
use Netgen\BlockManager\API\Values\Collection\Item as APIItem;
use Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait;
use Netgen\BlockManager\Core\Values\Value;

final class Item extends Value implements APIItem
{
    use ConfigAwareValueTrait;

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $collectionId;

    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    protected $definition;

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

    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    protected $cmsItem;

    public function getId()
    {
        return $this->id;
    }

    public function getCollectionId()
    {
        return $this->collectionId;
    }

    public function getDefinition()
    {
        return $this->definition;
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

    public function getCmsItem()
    {
        return $this->cmsItem;
    }

    public function isScheduled()
    {
        return false;
    }

    public function isVisible(DateTimeInterface $reference = null)
    {
        return true;
    }

    public function isValid()
    {
        return true;
    }
}
