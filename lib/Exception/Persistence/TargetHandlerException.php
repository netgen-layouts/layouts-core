<?php

namespace Netgen\BlockManager\Exception\Persistence;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;

class TargetHandlerException extends RuntimeException
{
    /**
     * @param string $persistenceType
     * @param string $targetType
     *
     * @return \Netgen\BlockManager\Exception\Persistence\TargetHandlerException
     */
    public static function noTargetHandler($persistenceType, $targetType)
    {
        return new self(
            sprintf(
                '%s target handler for "%s" target type does not exist.',
                $persistenceType,
                $targetType
            )
        );
    }

    /**
     * @param string $class
     *
     * @return \Netgen\BlockManager\Exception\Persistence\TargetHandlerException
     */
    public static function invalidTargetHandler($class)
    {
        return new self(
            sprintf(
                'Target handler "%s" needs to implement "%s" interface.',
                $class,
                TargetHandler::class
            )
        );
    }
}
