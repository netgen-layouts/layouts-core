<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Layout;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ConditionTypeException extends InvalidArgumentException implements Exception
{
    public static function noConditionType(string $conditionType): self
    {
        return new self(
            sprintf(
                'Condition type "%s" does not exist.',
                $conditionType
            )
        );
    }

    public static function noFormMapper(string $conditionType): self
    {
        return new self(
            sprintf(
                'Form mapper for "%s" condition type does not exist.',
                $conditionType
            )
        );
    }
}
