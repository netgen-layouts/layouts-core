<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Parameters;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ParameterBuilderException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameter
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function noParameter(string $parameter): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" name does not exist in the builder.',
                $parameter
            )
        );
    }

    /**
     * @param string $option
     * @param string $parameter
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function noOption(string $option, string $parameter): self
    {
        return new self(
            sprintf(
                'Option "%s" does not exist in the builder for "%s" parameter.',
                $option,
                $parameter
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function subCompound(): self
    {
        return new self('Compound parameters cannot be added to compound parameters.');
    }

    /**
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function nonCompound(): self
    {
        return new self('Parameters cannot be added to non-compound parameters.');
    }

    /**
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function invalidConstraints(): self
    {
        return new self('Parameter constraints need to be either a Symfony constraint or a closure.');
    }
}
