<?php

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class BlockException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $placeholder
     *
     * @return \Netgen\BlockManager\Exception\Core\BlockException
     */
    public static function noPlaceholder($placeholder)
    {
        return new self(
            sprintf(
                'Placeholder with "%s" identifier does not exist in the block.',
                $placeholder
            )
        );
    }
}
