<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Item\Stubs;

use Netgen\BlockManager\Item\ValueConverterInterface;

final class UnsupportedValueConverter implements ValueConverterInterface
{
    public function supports($object)
    {
        return false;
    }

    public function getValueType($object)
    {
        return 'value';
    }

    public function getId($object)
    {
        return $object->getId();
    }

    public function getRemoteId($object)
    {
        return $object->getRemoteId();
    }

    public function getName($object)
    {
        return 'Some value';
    }

    public function getIsVisible($object)
    {
        return $object->isVisible();
    }

    public function getObject($object)
    {
        return $object;
    }
}
