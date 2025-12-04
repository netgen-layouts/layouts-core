<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values;

interface ParameterStruct
{
    /**
     * Returns all parameter values from the struct.
     *
     * @var array<string, mixed>
     */
    public array $parameterValues { get; }

    /**
     * Sets the parameter value to the struct.
     *
     * The value needs to be in the domain format of the value for the parameter.
     */
    public function setParameterValue(string $parameterName, mixed $parameterValue): void;

    /**
     * Returns the parameter value with provided name or null if parameter does not exist.
     */
    public function getParameterValue(string $parameterName): mixed;

    /**
     * Returns if the struct has a parameter value with provided name.
     */
    public function hasParameterValue(string $parameterName): bool;
}
