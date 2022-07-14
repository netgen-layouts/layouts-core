<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Config;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ConfigDefinitionException extends InvalidArgumentException implements Exception
{
    public static function noConfigDefinition(string $configKey): self
    {
        return new self(
            sprintf(
                'Config definition with "%s" config key does not exist.',
                $configKey,
            ),
        );
    }
}
