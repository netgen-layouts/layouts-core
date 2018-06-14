<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Config;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ConfigDefinitionException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $type
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Config\ConfigDefinitionException
     */
    public static function noConfigDefinition(string $type, string $identifier): self
    {
        return new self(
            sprintf(
                'Config definition for "%s" type and "%s" identifier does not exist.',
                $type,
                $identifier
            )
        );
    }
}
