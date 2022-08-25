<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Countable;

interface ParameterBuilderInterface extends Countable
{
    /**
     * Returns the parameter name.
     */
    public function getName(): ?string;

    /**
     * Returns the parameter type.
     */
    public function getType(): ?ParameterTypeInterface;

    /**
     * Returns the parameter options.
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    /**
     * Returns the parameter option with provided name.
     *
     * @throws \Netgen\Layouts\Exception\InvalidArgumentException If the option does not exist
     *
     * @return mixed
     */
    public function getOption(string $name);

    /**
     * Returns if the parameter option with provided name exists.
     */
    public function hasOption(string $name): bool;

    /**
     * Sets the option to the provided value.
     *
     * This will cause all options to be reinitialized with the internal options resolver
     * in order to properly validate the new option value.
     *
     * The options will keep their existing values (unless the options resolver modifies them
     * according to rules in the parameter type).
     *
     * @param mixed $value
     */
    public function setOption(string $name, $value): self;

    /**
     * Returns if the parameter is required.
     */
    public function isRequired(): bool;

    /**
     * Sets if the parameter is required.
     */
    public function setRequired(bool $isRequired): self;

    /**
     * Sets if the parameter is readonly.
     */
    public function isReadOnly(): bool;

    /**
     * Sets if the parameter is readonly.
     */
    public function setReadOnly(bool $isReadOnly): self;

    /**
     * Returns the default value of the parameter.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Sets the default value of the parameter.
     *
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): self;

    /**
     * Returns the parameter label.
     *
     * @return string|bool|null
     */
    public function getLabel();

    /**
     * Sets the parameter label.
     *
     * @param string|bool|null $label
     */
    public function setLabel($label): self;

    /**
     * Returns the parameter groups.
     *
     * @return string[]
     */
    public function getGroups(): array;

    /**
     * Sets the parameter groups.
     *
     * @param string[] $groups
     */
    public function setGroups(array $groups): self;

    /**
     * Returns the runtime constraints for this parameter.
     *
     * @return array<\Symfony\Component\Validator\Constraint|\Closure>
     */
    public function getConstraints(): array;

    /**
     * Sets the parameter constraints.
     *
     * @param array<\Symfony\Component\Validator\Constraint|\Closure> $constraints
     */
    public function setConstraints(array $constraints): self;

    /**
     * Adds the parameter to the builder.
     *
     * @param array<string, mixed> $options
     */
    public function add(string $name, string $type, array $options = []): self;

    /**
     * Returns the builder for parameter with provided name.
     */
    public function get(string $name): self;

    /**
     * Returns the builders for all parameters, optionally filtered by the group.
     *
     * @return \Netgen\Layouts\Parameters\ParameterBuilderInterface[]
     */
    public function all(?string $group = null): array;

    /**
     * Returns if the builder has the parameter with provided name.
     */
    public function has(string $name): bool;

    /**
     * Removes the parameter from the builder.
     */
    public function remove(string $name): self;

    /**
     * Returns the count of the parameters.
     */
    public function count(): int;

    /**
     * Builds the parameter definitions.
     *
     * @return \Netgen\Layouts\Parameters\ParameterDefinition[]
     */
    public function buildParameterDefinitions(): array;
}
