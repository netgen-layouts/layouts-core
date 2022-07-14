<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Persistence;

use Netgen\Layouts\Exception\Exception;
use RuntimeException;

use function sprintf;

final class TargetHandlerException extends RuntimeException implements Exception
{
    public static function noTargetHandler(string $persistenceType, string $targetType): self
    {
        return new self(
            sprintf(
                '%s target handler for "%s" target type does not exist.',
                $persistenceType,
                $targetType,
            ),
        );
    }
}
