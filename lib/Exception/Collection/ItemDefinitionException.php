<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Collection;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ItemDefinitionException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\Exception\Collection\ItemDefinitionException
     */
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
