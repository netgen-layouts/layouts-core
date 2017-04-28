<?php

namespace Netgen\BlockManager\Exception\Block;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class BlockTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockTypeException
     */
    public static function noBlockType($identifier)
    {
        return new self(
            sprintf(
                'Block type with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockTypeException
     */
    public static function noBlockTypeGroup($identifier)
    {
        return new self(
            sprintf(
                'Block type group with "%s" identifier does not exist.',
                $identifier
            )
        );
    }
}
