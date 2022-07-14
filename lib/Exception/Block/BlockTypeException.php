<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Block;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class BlockTypeException extends InvalidArgumentException implements Exception
{
    public static function noBlockType(string $identifier): self
    {
        return new self(
            sprintf(
                'Block type with "%s" identifier does not exist.',
                $identifier,
            ),
        );
    }

    public static function noBlockTypeGroup(string $identifier): self
    {
        return new self(
            sprintf(
                'Block type group with "%s" identifier does not exist.',
                $identifier,
            ),
        );
    }
}
