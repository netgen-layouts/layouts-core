<?php

namespace Netgen\BlockManager\Item;

use Netgen\BlockManager\Value;

/**
 * NullItem represents a value from CMS which could not be
 * loaded (for example, if the value does not exist any more).
 */
final class NullItem extends Value implements ItemInterface
{
    /**
     * @var int|string
     */
    protected $value;

    public function getValue()
    {
        return $this->value;
    }

    public function getRemoteId()
    {
        return $this->value;
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
