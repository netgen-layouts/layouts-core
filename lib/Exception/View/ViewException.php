<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\View;

use Netgen\Layouts\Exception\Exception;
use RuntimeException;

use function sprintf;

final class ViewException extends RuntimeException implements Exception
{
    public static function parameterNotFound(string $parameterName, string $viewType): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" name was not found in "%s" view.',
                $parameterName,
                $viewType,
            ),
        );
    }
}
