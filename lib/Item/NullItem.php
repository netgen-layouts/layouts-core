<?php

namespace Netgen\BlockManager\Item;

/**
 * NullItem represents a value from CMS which could not be
 * loaded (for example, if the value does not exist any more).
 */
final class NullItem implements ItemInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getRemoteId()
    {
        return null;
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
