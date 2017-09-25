<?php

namespace Netgen\BlockManager\Exception\Layout;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ConditionTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $conditionType
     *
     * @return \Netgen\BlockManager\Exception\Layout\ConditionTypeException
     */
    public static function noConditionType($conditionType)
    {
        return new self(
            sprintf(
                'Condition type "%s" does not exist.',
                $conditionType
            )
        );
    }

    /**
     * @param string $conditionType
     *
     * @return \Netgen\BlockManager\Exception\Layout\ConditionTypeException
     */
    public static function noFormMapper($conditionType)
    {
        return new self(
            sprintf(
                'Form mapper for "%s" condition type does not exist.',
                $conditionType
            )
        );
    }
}
