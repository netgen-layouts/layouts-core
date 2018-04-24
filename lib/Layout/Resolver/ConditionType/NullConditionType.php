<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;

final class NullConditionType implements ConditionTypeInterface
{
    /**
     * @var string
     */
    private $conditionType;

    /**
     * @param string $conditionType
     */
    public function __construct($conditionType)
    {
        $this->conditionType = $conditionType;
    }

    public function getType()
    {
        return $this->conditionType;
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
