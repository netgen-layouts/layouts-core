<?php

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class ConfigException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $configKey
     *
     * @return \Netgen\BlockManager\Exception\Core\ConfigException
     */
    public static function noConfig($configKey)
    {
        return new self(
            sprintf(
                'Configuration with "%s" config key does not exist.',
                $configKey
            )
        );
    }

    /**
     * @param string $configKey
     *
     * @return \Netgen\BlockManager\Exception\Core\ConfigException
     */
    public static function configNotEnabled($configKey)
    {
        return new self(
            sprintf(
                'Config with "%s" config key is not enabled.',
                $configKey
            )
        );
    }

    /**
     * @param string $configKey
     *
     * @return \Netgen\BlockManager\Exception\Core\ConfigException
     */
    public static function noConfigStruct($configKey)
    {
        return new self(
            sprintf(
                'Config struct with config key "%s" does not exist.',
                $configKey
            )
        );
    }
}
