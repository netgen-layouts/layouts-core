<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Exception;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ConfigurationException extends InvalidArgumentException implements Exception
{
    public static function noParameter(string $parameterName): self
    {
        return new self(
            sprintf(
                'Parameter "%s" does not exist in configuration.',
                $parameterName
            )
        );
    }
}
