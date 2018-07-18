<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Config;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ConfigDefinitionException extends InvalidArgumentException implements Exception
{
    public static function noConfigDefinition(string $configKey): self
    {
        return new self(
            sprintf(
                'Config definition with "%s" config key does not exist.',
                $configKey
            )
        );
    }
}
