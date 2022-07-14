<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Exception;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ConfigurationException extends InvalidArgumentException implements Exception
{
    public static function noParameter(string $parameterName): self
    {
        return new self(
            sprintf(
                'Parameter "%s" does not exist in configuration.',
                $parameterName,
            ),
        );
    }
}
