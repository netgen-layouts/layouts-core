<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Persistence;

use Netgen\BlockManager\Exception\Exception;
use RuntimeException;

final class TargetHandlerException extends RuntimeException implements Exception
{
    /**
     * @param string $persistenceType
     * @param string $targetType
     *
     * @return \Netgen\BlockManager\Exception\Persistence\TargetHandlerException
     */
    public static function noTargetHandler(string $persistenceType, string $targetType): self
    {
        return new self(
            sprintf(
                '%s target handler for "%s" target type does not exist.',
                $persistenceType,
                $targetType
            )
        );
    }
}
