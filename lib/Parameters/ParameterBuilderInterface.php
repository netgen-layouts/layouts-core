<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Countable;

interface ParameterBuilderInterface extends Countable
{
    /**
     * Returns the parameter name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Returns the parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface|null
     */
    public function getType(): ?ParameterTypeInterface;

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions(): array;

    /**
     * Returns the parameter option with provided name.
     *
     * @param string $name
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the option does not exist
     *
     * @return mixed
     */
    public function getOption(string $name);

    /**
     * Returns if the parameter option with provided name exists.
     *
     * @param string $name
     *
     * @return bool
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
     * @param string $name
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setOption(string $name, $value): self;

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * Sets if the parameter is required.
     *
     * @param bool $isRequired
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setRequired(bool $isRequired): self;

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
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
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
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setLabel($label): self;

    /**
     * Returns the parameter groups.
     *
     * @return array
     */
    public function getGroups(): array;

    /**
     * Sets the parameter groups.
     *
     * @param array $groups
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setGroups(array $groups): self;

    /**
     * Returns the runtime constraints for this parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints(): array;

    /**
     * Sets the parameter constraints.
     *
     * @param array $constraints
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setConstraints(array $constraints): self;

    /**
     * Adds the parameter to the builder.
     *
     * @param string $name
     * @param string $type
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function add(string $name, string $type, array $options = []): self;

    /**
     * Returns the builder for parameter with provided name.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function get(string $name): self;

    /**
     * Returns the builders for all parameters, optionally filtered by the group.
     *
     * @param string $group
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface[]
     */
    public function all(string $group = null): array;

    /**
     * Returns if the builder has the parameter with provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Removes the parameter from the builder.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function remove(string $name): self;

    /**
     * Returns the count of the parameters.
     *
     * @return int
     */
    public function count(): int;

    /**
     * Builds the parameter definitions.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    public function buildParameterDefinitions(): array;
}
