<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Transfer;

use Netgen\Layouts\Exception\Exception;
use RuntimeException;
use Throwable;

use function sprintf;

final class ImportException extends RuntimeException implements Exception
{
    public static function importError(Throwable $previous): self
    {
        return new self(
            sprintf(
                'There was an error importing entities: %s',
                $previous->getMessage(),
            ),
            0,
            $previous,
        );
    }
}
