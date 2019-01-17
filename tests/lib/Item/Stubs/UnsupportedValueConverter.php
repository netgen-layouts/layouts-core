<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Item\ValueConverterInterface;

final class UnsupportedValueConverter implements ValueConverterInterface
{
    public function supports($object): bool
    {
        return false;
    }

    public function getValueType($object): string
    {
        return 'value';
    }

    /**
     * @param \Netgen\BlockManager\Tests\Item\Stubs\Value $object
     *
     * @return int|string
     */
    public function getId($object)
    {
        return $object->getId();
    }

    /**
     * @param \Netgen\BlockManager\Tests\Item\Stubs\Value $object
     *
     * @return int|string
     */
    public function getRemoteId($object)
    {
        return $object->getRemoteId();
    }

    public function getName($object): string
    {
        return 'Some value';
    }

    /**
     * @param \Netgen\BlockManager\Tests\Item\Stubs\Value $object
     */
    public function getIsVisible($object): bool
    {
        return $object->isVisible();
    }

    public function getObject($object)
    {
        return $object;
    }
}
