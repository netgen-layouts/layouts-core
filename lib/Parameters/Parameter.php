<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Utils\HydratorTrait;

final class Parameter
{
    use HydratorTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Netgen\Layouts\Parameters\ParameterDefinition
     */
    private $parameterDefinition;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $isEmpty = true;

    /**
     * Returns the string representation of the parameter value.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Returns the parameter name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the parameter definition.
     */
    public function getParameterDefinition(): ParameterDefinition
    {
        return $this->parameterDefinition;
    }

    /**
     * Returns the parameter value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns if the parameter value is empty.
     */
    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }
}
