<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\App\Item\ValueConverter;

use Netgen\Layouts\Item\ValueConverterInterface;
use Netgen\Layouts\Tests\App\Item\Value;

/**
 * @implements \Netgen\Layouts\Item\ValueConverterInterface<\Netgen\Layouts\Tests\App\Item\Value>
 */
final class MyValueTypeValueConverter implements ValueConverterInterface
{
    public function supports(object $object): bool
    {
        return $object instanceof Value;
    }

    public function getValueType(object $object): string
    {
        return 'my_value_type';
    }

    public function getId(object $object): int
    {
        return $object->id;
    }

    public function getRemoteId(object $object): int
    {
        return $object->id;
    }

    public function getName(object $object): string
    {
        return 'Value with ID #' . $object->id;
    }

    public function getIsVisible(object $object): bool
    {
        return true;
    }

    public function getObject(object $object): Value
    {
        return $object;
    }
}
