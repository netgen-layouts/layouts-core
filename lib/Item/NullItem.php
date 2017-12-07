<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\ValueObject;

/**
 * NullItem represents a value from CMS which could not be
 * loaded (for example, if the value does not exist any more).
 */
final class NullItem extends ValueObject implements ItemInterface
{
    /**
     * @var int|string
     */
    protected $valueId;

    public function getValueId()
    {
        return $this->valueId;
    }

    public function getRemoteId()
    {
        return $this->valueId;
    }

    public function getValueType()
    {
        return 'null';
    }

    public function getName()
    {
        return '(INVALID ITEM)';
    }

    public function isVisible()
    {
        return true;
    }

    public function getObject()
    {
        return null;
    }
}
