<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_key_exists;

/**
 * The definition of a parameter, specifying its name, type and various options.
 */
class ParameterDefinition
{
    use HydratorTrait;

    /**
     * Returns the parameter name.
     */
    final public protected(set) string $name;

    /**
     * Returns the parameter type.
     */
    final public protected(set) ParameterTypeInterface $type;

    /**
     * Returns the parameter options.
     *
     * @var array<string, mixed>
     */
    final public protected(set) array $options = [];

    /**
     * Returns if the parameter is required.
     */
    final public protected(set) bool $isRequired;

    /**
     * Returns if the parameter is readonly. A readonly parameter can be set only when creating a block.
     */
    final public protected(set) bool $isReadOnly;

    /**
     * Returns the default parameter value.
     */
    final public protected(set) mixed $defaultValue;

    /**
     * Returns the parameter label.
     */
    final public protected(set) string|false|null $label;

    /**
     * Returns the list of all parameter groups.
     *
     * @var string[]
     */
    final public protected(set) array $groups = [];

    /**
     * Returns the list of constraints.
     *
     * These can either be instances of Symfony constraints (\Symfony\Component\Validator\Constraint)
     * or closures that return a Symfony constraint each.
     *
     * @var array<\Symfony\Component\Validator\Constraint|\Closure>
     */
    final public protected(set) array $constraints = [];

    /**
     * Returns if the provided parameter option exists.
     */
    public function hasOption(string $option): bool
    {
        return array_key_exists($option, $this->options);
    }

    /**
     * Returns the provided parameter option.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterException If option does not exist
     */
    public function getOption(string $option): mixed
    {
        if (!$this->hasOption($option)) {
            throw ParameterException::noOption($option);
        }

        return $this->options[$option];
    }
}
