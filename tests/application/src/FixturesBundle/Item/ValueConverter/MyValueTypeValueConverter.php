<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Bundle\FixturesBundle\Item\ValueConverter;

use Netgen\BlockManager\Item\ValueConverterInterface;
use Netgen\BlockManager\Tests\Bundle\FixturesBundle\Item\Value;

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

    public function getId(object $object)
    {
        return $object->id;
    }

    public function getRemoteId(object $object)
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

    public function getObject(object $object): object
    {
        return $object;
    }
}
