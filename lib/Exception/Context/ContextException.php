<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Context;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ContextException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $variableName
     *
     * @return \Netgen\BlockManager\Exception\Context\ContextException
     */
    public static function noVariable($variableName)
    {
        return new self(
            sprintf(
                'Variable "%s" does not exist in the context.',
                $variableName
            )
        );
    }
}
