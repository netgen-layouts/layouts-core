<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Collection;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

final class ItemDefinitionException extends InvalidArgumentException implements Exception
{
    public static function noItemDefinition(string $valueType): self
    {
        return new self(
            sprintf(
                'Item definition for "%s" value type does not exist.',
                $valueType
            )
        );
    }
}
