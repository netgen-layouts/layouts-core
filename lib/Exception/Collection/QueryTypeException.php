<?php

namespace Netgen\BlockManager\Exception\Collection;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class QueryTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Collection\QueryTypeException
     */
    public static function noQueryType($identifier)
    {
        return new self(
            sprintf(
                'Query type with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * @param string $queryType
     * @param string $form
     *
     * @return \Netgen\BlockManager\Exception\Collection\QueryTypeException
     */
    public static function noForm($queryType, $form)
    {
        return new self(
            sprintf(
                'Form "%s" does not exist in "%s" query type.',
                $form,
                $queryType
            )
        );
    }
}
