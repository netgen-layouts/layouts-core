<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ValueConverterInterface;

final class UnsupportedValueConverter implements ValueConverterInterface
{
    public function supports(object $object): bool
    {
        return false;
    }

    public function getValueType(object $object): string
    {
        return 'value';
    }

    /**
     * @param \Netgen\Layouts\Tests\Item\Stubs\Value $object
     *
     * @return int|string
     */
    public function getId(object $object)
    {
        return $object->getId();
    }

    /**
     * @param \Netgen\Layouts\Tests\Item\Stubs\Value $object
     *
     * @return int|string
     */
    public function getRemoteId(object $object)
    {
        return $object->getRemoteId();
    }

    public function getName(object $object): string
    {
        return 'Some value';
    }

    /**
     * @param \Netgen\Layouts\Tests\Item\Stubs\Value $object
     */
    public function getIsVisible(object $object): bool
    {
        return $object->isVisible();
    }

    public function getObject(object $object): object
    {
        return $object;
    }
}
