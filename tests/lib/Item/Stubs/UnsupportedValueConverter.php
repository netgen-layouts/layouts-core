<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Item\Stubs;

use Netgen\Layouts\Item\ValueConverterInterface;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\Netgen\Layouts\Tests\Item\Stubs\Value>
 */
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

    public function getId(object $object): int
    {
        return $object->getId();
    }

    public function getRemoteId(object $object): string
    {
        return $object->getRemoteId();
    }

    public function getName(object $object): string
    {
        return 'Some value';
    }

    public function getIsVisible(object $object): bool
    {
        return $object->isVisible();
    }

    public function getObject(object $object): object
    {
        return $object;
    }
}
