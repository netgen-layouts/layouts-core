<?php

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class ConfigException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Core\ConfigException
     */
    public static function noConfig($identifier)
    {
        return new self(
            sprintf(
                'Configuration with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Core\ConfigException
     */
    public static function configNotEnabled($identifier)
    {
        return new self(
            sprintf(
                'Config with "%s" identifier is not enabled.',
                $identifier
            )
        );
    }

    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Core\ConfigException
     */
    public static function noConfigStruct($identifier)
    {
        return new self(
            sprintf(
                'Config struct with identifier "%s" does not exist.',
                $identifier
            )
        );
    }
}
