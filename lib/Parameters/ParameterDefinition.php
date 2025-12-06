<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_key_exists;

/**
 * The definition of a parameter, specifying its name, type and various options.
 */
final class ParameterDefinition implements ParameterDefinitionCollectionInterface
{
    use HydratorTrait;
    use ParameterDefinitionCollectionTrait;

    /**
     * Returns the parameter name.
     */
    public private(set) string $name;

    /**
     * Returns the parameter type.
     */
    public private(set) ParameterTypeInterface $type;

    /**
     * Returns the parameter options.
     *
     * @var array<string, mixed>
     */
    public private(set) array $options = [];

    /**
     * Returns if the parameter is required.
     */
    public private(set) bool $isRequired;

    /**
     * Returns if the parameter is readonly. A readonly parameter can be set only when creating a block.
     */
    public private(set) bool $isReadOnly;

    /**
     * Returns if the parameter is translatable.
     */
    public private(set) bool $isTranslatable;

    /**
     * Returns if the parameter is compound. A compound parameter can contain other parameters.
     */
    public bool $isCompound {
        get => $this->type instanceof CompoundParameterTypeInterface;
    }

    /**
     * Returns the default parameter value.
     */
    public private(set) mixed $defaultValue;

    /**
     * Returns the parameter label.
     */
    public private(set) string|false|null $label;

    /**
     * Returns the list of all parameter groups.
     *
     * @var string[]
     */
    public private(set) array $groups = [];

    /**
     * Returns the list of constraints.
     *
     * These can either be instances of Symfony constraints (\Symfony\Component\Validator\Constraint)
     * or closures that return a Symfony constraint each.
     *
     * @var array<\Symfony\Component\Validator\Constraint|\Closure>
     */
    public private(set) array $constraints = [];

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
