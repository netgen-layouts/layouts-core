<?php

namespace Netgen\BlockManager\Exception\Item;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ValueException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\Exception\Item\ValueException
     */
    public static function noValueLoader($valueType)
    {
        return new self(
            sprintf(
                'Value loader for "%s" value type does not exist.',
                $valueType
            )
        );
    }

    /**
     * @param string $type
     *
     * @return \Netgen\BlockManager\Exception\Item\ValueException
     */
    public static function noValueConverter($type)
    {
        return new self(
            sprintf(
                'Value converter for "%s" type does not exist.',
                $type
            )
        );
    }

    /**
     * @param string $valueType
     *
     * @return \Netgen\BlockManager\Exception\Item\ValueException
     */
    public static function noValueUrlBuilder($valueType)
    {
        return new self(
            sprintf(
                'Value URL builder for "%s" value type does not exist.',
                $valueType
            )
        );
    }
}
