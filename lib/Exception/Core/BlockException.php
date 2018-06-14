<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class BlockException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $placeholder
     *
     * @return \Netgen\BlockManager\Exception\Core\BlockException
     */
    public static function noPlaceholder(string $placeholder): self
    {
        return new self(
            sprintf(
                'Placeholder with "%s" identifier does not exist in the block.',
                $placeholder
            )
        );
    }

    /**
     * @param string $collection
     *
     * @return \Netgen\BlockManager\Exception\Core\BlockException
     */
    public static function noCollection(string $collection): self
    {
        return new self(
            sprintf(
                'Collection with "%s" identifier does not exist in the block.',
                $collection
            )
        );
    }
}
