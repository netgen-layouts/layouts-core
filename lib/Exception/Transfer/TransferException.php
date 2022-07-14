<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Transfer;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class TransferException extends InvalidArgumentException implements Exception
{
    public static function noEntityHandler(string $entityType): self
    {
        return new self(
            sprintf(
                'Entity handler for "%s" entity type does not exist.',
                $entityType,
            ),
        );
    }
}
