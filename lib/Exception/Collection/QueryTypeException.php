<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Collection;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class QueryTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Collection\QueryTypeException
     */
    public static function noQueryType(string $identifier): self
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
    public static function noForm(string $queryType, string $form): self
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
