<?php

namespace Netgen\BlockManager\Exception\Persistence;

use Netgen\BlockManager\Exception\RuntimeException;

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
}
