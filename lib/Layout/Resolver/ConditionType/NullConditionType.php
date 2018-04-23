<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;

final class NullConditionType implements ConditionTypeInterface
{
    public function getType()
    {
        return 'null';
    }

    public function getConstraints()
    {
        return [];
    }

    public function matches(Request $request, $value)
    {
        return true;
    }
}
