<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Closure;
use Netgen\Layouts\Exception\BadMethodCallException;
use Netgen\Layouts\Exception\Parameters\ParameterBuilderException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;

use function array_all;
use function array_filter;
use function array_key_exists;
use function count;
use function in_array;
use function is_a;
use function is_bool;
use function sprintf;

final class ParameterBuilder implements ParameterBuilderInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $options;

    private bool $isRequired = false;

    private bool $isReadOnly = false;

    private bool $isTranslatable = false;

    private mixed $defaultValue;

    private string|false|null $label;

    /**
     * @var string[]
     */
    private array $groups = [];

    /**
     * @var array<\Symfony\Component\Validator\Constraint|\Closure>
     */
    private array $constraints = [];

    /**
     * @var \Netgen\Layouts\Parameters\ParameterBuilderInterface[]
     */
    private array $unresolvedChildren = [];

    /**
     * @var \Netgen\Layouts\Parameters\ParameterDefinition[]
     */
    private array $resolvedChildren = [];

    private bool $isLocked = false;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private ParameterBuilderFactory $builderFactory,
        private ?string $name = null,
        private ?ParameterTypeInterface $type = null,
        array $options = [],
        private ?ParameterBuilderInterface $parentBuilder = null,
        private bool $supportsTranslatableParameters = false,
    ) {
        $this->options = $this->resolveOptions($options);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): ?ParameterTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name): mixed
    {
        if (!array_key_exists($name, $this->options)) {
            throw ParameterBuilderException::noOption($name, $this->name);
        }

        return $this->options[$name];
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    public function setOption(string $name, mixed $value): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the options is not possible after parameters have been built.');
        }

        $options = $this->options + [
            'required' => $this->isRequired,
            'readonly' => $this->isReadOnly,
            'translatable' => $this->isTranslatable,
            'default_value' => $this->defaultValue,
            'label' => $this->label,
            'groups' => $this->groups,
            'constraints' => $this->constraints,
        ];

        $options[$name] = $value;

        $this->options = $this->resolveOptions($options);

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    public function setRequired(bool $isRequired): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the required flag is not possible after parameters have been built.');
        }

        $this->isRequired = $isRequired;

        return $this;
    }

    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    public function setReadOnly(bool $isReadOnly): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the readonly flag is not possible after parameters have been built.');
        }

        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    public function isCompound(): bool
    {
        return $this->type instanceof CompoundParameterTypeInterface;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(mixed $defaultValue): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the default value is not possible after parameters have been built.');
        }

        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function getLabel(): string|false|null
    {
        return $this->label;
    }

    public function setLabel(string|false|null $label): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the label is not possible after parameters have been built.');
        }

        $this->label = $label;

        return $this;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function setGroups(array $groups): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the groups is not possible after parameters have been built.');
        }

        $this->groups = $groups;

        return $this;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function setConstraints(array $constraints): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Setting the constraints is not possible after parameters have been built.');
        }

        if (!$this->validateConstraints($constraints)) {
            throw ParameterBuilderException::invalidConstraints();
        }

        $this->constraints = $constraints;

        return $this;
    }

    public function add(string $name, string $type, array $options = []): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Parameters cannot be added after they have been built.');
        }

        if (
            $this->type instanceof CompoundParameterTypeInterface
            && is_a($type, CompoundParameterTypeInterface::class, true)
        ) {
            throw ParameterBuilderException::subCompound();
        }

        if ($this->type !== null && !$this->type instanceof CompoundParameterTypeInterface) {
            throw ParameterBuilderException::nonCompound();
        }

        $this->unresolvedChildren[$name] = $this->builderFactory->createParameterBuilder(
            [
                'name' => $name,
                'type' => $type,
                'options' => $options,
                'parent' => $this,
            ],
            $this->supportsTranslatableParameters,
        );

        return $this;
    }

    public function get(string $name): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Accessing parameter builders is not possible after parameters have been built.');
        }

        if (!$this->has($name)) {
            throw ParameterBuilderException::noParameter($name);
        }

        return $this->unresolvedChildren[$name];
    }

    public function all(?string $group = null): array
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Accessing parameter builders is not possible after parameters have been built.');
        }

        return array_filter(
            $this->unresolvedChildren,
            static fn (ParameterBuilderInterface $builder): bool => $group === null || in_array($group, $builder->getGroups(), true),
        );
    }

    public function has(string $name): bool
    {
        return isset($this->unresolvedChildren[$name]);
    }

    public function remove(string $name): ParameterBuilderInterface
    {
        if ($this->isLocked) {
            throw new BadMethodCallException('Removing parameters is not possible after parameters have been built.');
        }

        unset($this->unresolvedChildren[$name]);

        return $this;
    }

    public function count(): int
    {
        return count($this->unresolvedChildren);
    }

    public function buildParameterDefinitions(): array
    {
        if ($this->isLocked) {
            return $this->resolvedChildren;
        }

        foreach ($this->unresolvedChildren as $name => $builder) {
            $this->resolvedChildren[$name] = $this->buildParameterDefinition($builder);
        }

        $this->isLocked = true;

        return $this->resolvedChildren;
    }

    /**
     * Builds the parameter definition.
     */
    private function buildParameterDefinition(ParameterBuilderInterface $builder): ParameterDefinition
    {
        $data = [
            'name' => $builder->getName(),
            'type' => $builder->getType(),
            'options' => $builder->getOptions(),
            'isRequired' => $builder->isRequired(),
            'isReadOnly' => $builder->isReadOnly(),
            'isTranslatable' => $builder->isTranslatable(),
            'defaultValue' => $builder->getDefaultValue(),
            'label' => $builder->getLabel(),
            'groups' => $builder->getGroups(),
            'constraints' => $builder->getConstraints(),
        ];

        // We build the sub parameters in order to lock the child builders
        $subParameters = $builder->buildParameterDefinitions();

        if ($builder->isCompound()) {
            $data['parameterDefinitions'] = $subParameters;
        }

        return ParameterDefinition::fromArray($data);
    }

    /**
     * Resolves the parameter options.
     *
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function resolveOptions(array $options): array
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->define('required')
            ->required()
            ->default(false)
            ->allowedTypes('bool');

        $optionsResolver
            ->define('readonly')
            ->required()
            ->default(false)
            ->allowedTypes('bool');

        $optionsResolver
            ->define('default_value')
            ->required()
            ->default(null);

        $optionsResolver
            ->define('label')
            ->required()
            ->default(null)
            ->allowedTypes('string', 'null', 'bool')
            ->allowedValues(static fn ($value): bool => !is_bool($value) || $value === false)
            ->info('It must be a string, null or false.');

        $optionsResolver
            ->define('groups')
            ->required()
            ->default([])
            ->allowedTypes('string[]')
            ->normalize(
                function (Options $options, array $value): array {
                    if (!$this->parentBuilder instanceof ParameterBuilderInterface) {
                        return $value;
                    }

                    if (!$this->parentBuilder->getType() instanceof CompoundParameterTypeInterface) {
                        return $value;
                    }

                    return $this->parentBuilder->getGroups();
                },
            );

        $optionsResolver
            ->define('constraints')
            ->required()
            ->default([])
            ->allowedTypes('array')
            ->allowedValues(fn (array $constraints): bool => $this->validateConstraints($constraints))
            ->info('It must be an array of constraints or closures.');

        if ($this->supportsTranslatableParameters) {
            $optionsResolver
                ->define('translatable')
                ->required()
                ->default(true)
                ->allowedTypes('bool')
                ->allowedValues(
                    function (bool $value): bool {
                        if (!$this->parentBuilder instanceof ParameterBuilderInterface) {
                            return true;
                        }

                        if (!$this->parentBuilder->getType() instanceof CompoundParameterTypeInterface) {
                            return true;
                        }

                        if ($value !== $this->parentBuilder->isTranslatable()) {
                            if ($value) {
                                throw new InvalidOptionsException(
                                    sprintf(
                                        'Parameter "%s" cannot be translatable, since its parent parameter "%s" is not translatable',
                                        $this->name ?? '',
                                        $this->parentBuilder->getName() ?? '',
                                    ),
                                );
                            }

                            throw new InvalidOptionsException(
                                sprintf(
                                    'Parameter "%s" needs to be translatable, since its parent parameter "%s" is translatable',
                                    $this->name ?? '',
                                    $this->parentBuilder->getName() ?? '',
                                ),
                            );
                        }

                        return true;
                    },
                )->info('It must be translatable depending if the parent is translatable or not.');
        }

        if ($this->type instanceof ParameterTypeInterface) {
            $this->type->configureOptions($optionsResolver);
        }

        if (!$this->supportsTranslatableParameters) {
            unset($options['translatable']);
        }

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->isRequired = $resolvedOptions['required'];
        $this->isReadOnly = $resolvedOptions['readonly'];
        $this->isTranslatable = $resolvedOptions['translatable'] ?? false;
        $this->defaultValue = $resolvedOptions['default_value'];
        $this->label = $resolvedOptions['label'];
        $this->groups = $resolvedOptions['groups'];
        $this->constraints = $resolvedOptions['constraints'];

        unset(
            $resolvedOptions['required'],
            $resolvedOptions['readonly'],
            $resolvedOptions['translatable'],
            $resolvedOptions['default_value'],
            $resolvedOptions['label'],
            $resolvedOptions['groups'],
            $resolvedOptions['constraints'],
        );

        return $resolvedOptions;
    }

    /**
     * Validates the list of constraints to be either a Symfony constraint or a closure.
     *
     * @param mixed[] $constraints
     */
    private function validateConstraints(array $constraints): bool
    {
        return array_all(
            $constraints,
            static fn (mixed $constraint): bool => $constraint instanceof Closure || $constraint instanceof Constraint,
        );
    }
}
