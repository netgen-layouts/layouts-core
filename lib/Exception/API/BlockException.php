<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\API;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class BlockException extends InvalidArgumentException implements Exception
{
    public static function noPlaceholder(string $placeholder): self
    {
        return new self(
            sprintf(
                'Placeholder with "%s" identifier does not exist in the block.',
                $placeholder,
            ),
        );
    }

    public static function noCollection(string $collection): self
    {
        return new self(
            sprintf(
                'Collection with "%s" identifier does not exist in the block.',
                $collection,
            ),
        );
    }
}
