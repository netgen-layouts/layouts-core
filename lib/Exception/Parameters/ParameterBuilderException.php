<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Parameters;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ParameterBuilderException extends InvalidArgumentException implements Exception
{
    public static function noParameter(string $parameter): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" name does not exist in the builder.',
                $parameter,
            ),
        );
    }

    public static function noOption(string $option, ?string $parameter = null): self
    {
        if ($parameter === null) {
            return new self(
                sprintf(
                    'Option "%s" does not exist in the builder.',
                    $option,
                ),
            );
        }

        return new self(
            sprintf(
                'Option "%s" does not exist in the builder for "%s" parameter.',
                $option,
                $parameter,
            ),
        );
    }

    public static function subCompound(): self
    {
        return new self('Compound parameters cannot be added to compound parameters.');
    }

    public static function nonCompound(): self
    {
        return new self('Parameters cannot be added to non-compound parameters.');
    }

    public static function invalidConstraints(): self
    {
        return new self('Parameter constraints need to be either a Symfony constraint or a closure.');
    }
}
