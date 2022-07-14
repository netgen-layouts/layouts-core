<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Collection;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class QueryTypeException extends InvalidArgumentException implements Exception
{
    public static function noQueryType(string $identifier): self
    {
        return new self(
            sprintf(
                'Query type with "%s" identifier does not exist.',
                $identifier,
            ),
        );
    }

    public static function noForm(string $queryType, string $form): self
    {
        return new self(
            sprintf(
                'Form "%s" does not exist in "%s" query type.',
                $form,
                $queryType,
            ),
        );
    }
}
