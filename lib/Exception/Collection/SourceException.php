<?php

namespace Netgen\BlockManager\Exception\Collection;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class SourceException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Collection\SourceException
     */
    public static function noSource($identifier)
    {
        return new self(
            sprintf(
                'Source with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * @param string $source
     * @param string $query
     *
     * @return \Netgen\BlockManager\Exception\Collection\SourceException
     */
    public static function noQuery($source, $query)
    {
        return new self(
            sprintf(
                'Query with "%s" identifier does not exist in "%s" source.',
                $query,
                $source
            )
        );
    }
}
