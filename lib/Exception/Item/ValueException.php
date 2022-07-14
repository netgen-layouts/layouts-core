<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Item;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ValueException extends InvalidArgumentException implements Exception
{
    public static function noValueLoader(string $valueType): self
    {
        return new self(
            sprintf(
                'Value loader for "%s" value type does not exist.',
                $valueType,
            ),
        );
    }

    public static function noValueConverter(string $type): self
    {
        return new self(
            sprintf(
                'Value converter for "%s" type does not exist.',
                $type,
            ),
        );
    }
}
