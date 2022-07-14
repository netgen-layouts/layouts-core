<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Context;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ContextException extends InvalidArgumentException implements Exception
{
    public static function noVariable(string $variableName): self
    {
        return new self(
            sprintf(
                'Variable "%s" does not exist in the context.',
                $variableName,
            ),
        );
    }
}
