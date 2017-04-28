<?php

namespace Netgen\BlockManager\Exception\View;

use Netgen\BlockManager\Exception\Exception;
use RuntimeException;

class ViewException extends RuntimeException implements Exception
{
    /**
     * @param string $parameterName
     * @param string $viewType
     *
     * @return \Netgen\BlockManager\Exception\View\ViewException
     */
    public static function parameterNotFound($parameterName, $viewType)
    {
        return new self(
            sprintf(
                'Parameter with "%s" name was not found in "%s" view.',
                $parameterName,
                $viewType
            )
        );
    }
}
