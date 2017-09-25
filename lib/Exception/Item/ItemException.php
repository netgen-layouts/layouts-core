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
     * @param string $valueId
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function noValue($valueId)
    {
        return new self(
            sprintf(
                'Value with ID %s does not exist.',
                $valueId
            )
        );
    }

    /**
     * @param string $valueId
     *
     * @return \Netgen\BlockManager\Exception\Item\ItemException
     */
    public static function invalidValue($valueId)
    {
        return new self(
            sprintf(
                'Item "%s" is not valid.',
                $valueId
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
