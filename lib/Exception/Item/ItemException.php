<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Item;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ItemException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function noValueType(string $valueType): self
    {
        return new self(
            sprintf(
                'Value type "%s" does not exist.',
                $valueType
            )
        );
    }

    /**
     * @param string|int $value
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function noValue($value): self
    {
        return new self(
            sprintf(
                'Value with (remote) ID %s does not exist.',
                $value
            )
        );
    }

    /**
     * @param string|int $value
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function invalidValue($value): self
    {
        return new self(
            sprintf(
                'Item "%s" is not valid.',
                $value
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function canNotLoadItem(): self
    {
        return new self('Item could not be loaded.');
    }
}
