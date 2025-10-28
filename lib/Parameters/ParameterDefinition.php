<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Utils\HydratorTrait;

use function array_key_exists;

/**
 * The definition of a parameter, specifying its name, type and various options.
 *
 * This class is considered final and should not be extended.
 */
class ParameterDefinition
{
    use HydratorTrait;

    protected string $name;

    protected ParameterTypeInterface $type;

    /**
     * @var array<string, mixed>
     */
    protected array $options = [];

    protected bool $isRequired;

    protected bool $isReadOnly;

    protected mixed $defaultValue;

    protected string|false|null $label;

    /**
     * @var string[]
     */
    protected array $groups = [];

    /**
     * @var array<\Symfony\Component\Validator\Constraint|\Closure>
     */
    protected array $constraints = [];

    /**
     * Returns the parameter name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the parameter type.
     */
    public function getType(): ParameterTypeInterface
    {
        return $this->type;
    }

    /**
     * Returns the parameter options.
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

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

    /**
     * Returns if the parameter is required.
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * Returns if the parameter is readonly. A readonly parameter can be set only when creating a block.
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * Returns the default parameter value.
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * Returns the parameter label.
     */
    public function getLabel(): string|false|null
    {
        return $this->label;
    }

    /**
     * Returns the list of all parameter groups.
     *
     * @return string[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Returns the list of constraints.
     *
     * These can either be instances of Symfony constraints (\Symfony\Component\Validator\Constraint)
     * or closures that return a Symfony constraint each.
     *
     * @return array<\Symfony\Component\Validator\Constraint|\Closure>
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }
}
