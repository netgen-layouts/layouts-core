<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;

final class NullTargetType implements TargetTypeInterface
{
    public function getType()
    {
        return 'null';
    }

    public function getConstraints()
    {
        return [];
    }

    public function provideValue(Request $request)
    {
    }
}
