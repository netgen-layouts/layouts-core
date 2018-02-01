<?php

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
    public static function noValueType($valueType)
    {
        return new self(
            sprintf(
                'Value type "%s" does not exist.',
                $valueType
            )
        );
    }

    /**
     * @param string $value
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function noValue($value)
    {
        return new self(
            sprintf(
                'Value with (remote) ID %s does not exist.',
                $value
            )
        );
    }

    /**
     * @param string $value
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function invalidValue($value)
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
    public static function canNotLoadItem()
    {
        return new self('Item could not be loaded.');
    }
}
