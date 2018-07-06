<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Closure;
use Netgen\BlockManager\Exception\BadMethodCallException;
use Netgen\BlockManager\Exception\Parameters\ParameterBuilderException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;

class ParameterBuilder implements ParameterBuilderInterface
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderInterface|null
     */
    protected $parentBuilder;
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    private $builderFactory;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface|null
     */
    private $type;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var bool
     */
    private $isRequired = false;

    /**
     * @var mixed
     */
    private $defaultValue;

    /**
     * @var string|null
     */
    private $label;

    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var \Symfony\Component\Validator\Constraint[]
     */
    private $constraints = [];

    /**
     * @var array
     */
    private $unresolvedChildren = [];

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinition[]
     */
    private $resolvedChildren = [];

    /**
     * @var bool
     */
    private $locked = false;

    public function __construct(
        ParameterBuilderFactoryInterface $builderFactory,
        ?string $name = null,
        ?ParameterTypeInterface $type = null,
        array $options = [],
        ?ParameterBuilderInterface $parentBuilder = null
    ) {
        $this->builderFactory = $builderFactory;

        $this->name = $name;
        $this->type = $type;
        $this->parentBuilder = $parentBuilder;

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

    public function getOption(string $name)
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

    public function setOption(string $name, $value): ParameterBuilderInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('Setting the options is not possible after parameters have been built.');
        }

        $options = $this->options + [
            'required' => $this->isRequired,
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
        if ($this->locked) {
            throw new BadMethodCallException('Setting the required flag is not possible after parameters have been built.');
        }

        $this->isRequired = $isRequired;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue): ParameterBuilderInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('Setting the default value is not possible after parameters have been built.');
        }

        $this->defaultValue = $defaultValue;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label): ParameterBuilderInterface
    {
        if ($this->locked) {
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
        if ($this->locked) {
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
        if ($this->locked) {
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
        if ($this->locked) {
            throw new BadMethodCallException('Parameters cannot be added after they have been built.');
        }

        if (
            $this->type instanceof CompoundParameterTypeInterface &&
            is_a($type, CompoundParameterTypeInterface::class, true)
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
            ]
        );

        return $this;
    }

    public function get(string $name): ParameterBuilderInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('Accessing parameter builders is not possible after parameters have been built.');
        }

        if (!$this->has($name)) {
            throw ParameterBuilderException::noParameter($name);
        }

        return $this->unresolvedChildren[$name];
    }

    public function all(?string $group = null): array
    {
        if ($this->locked) {
            throw new BadMethodCallException('Accessing parameter builders is not possible after parameters have been built.');
        }

        return array_filter(
            $this->unresolvedChildren,
            function (ParameterBuilderInterface $builder) use ($group): bool {
                if ($group === null) {
                    return true;
                }

                return in_array($group, $builder->getGroups(), true);
            }
        );
    }

    public function has(string $name): bool
    {
        return isset($this->unresolvedChildren[$name]);
    }

    public function remove(string $name): ParameterBuilderInterface
    {
        if ($this->locked) {
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
        if ($this->locked) {
            return $this->resolvedChildren;
        }

        if ($this->type instanceof CompoundParameterTypeInterface) {
            $this->type->buildParameters($this);
        }

        foreach ($this->unresolvedChildren as $name => $builder) {
            $this->resolvedChildren[$name] = $this->buildParameterDefinition($builder);
        }

        $this->locked = true;

        return $this->resolvedChildren;
    }

    /**
     * Configures the parameter options.
     */
    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
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
            'defaultValue' => $builder->getDefaultValue(),
            'label' => $builder->getLabel(),
            'groups' => $builder->getGroups(),
            'constraints' => $builder->getConstraints(),
        ];

        // We build the sub parameters in order to lock the child builders
        $subParameters = $builder->buildParameterDefinitions();

        if (!$builder->getType() instanceof CompoundParameterTypeInterface) {
            return new ParameterDefinition($data);
        }

        $data['parameterDefinitions'] = $subParameters;

        return new CompoundParameterDefinition($data);
    }

    /**
     * Resolves the parameter options.
     */
    private function resolveOptions(array $options): array
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('label', null);
        $optionsResolver->setDefault('groups', []);
        $optionsResolver->setDefault('constraints', []);

        if ($this->type instanceof ParameterTypeInterface) {
            $this->type->configureOptions($optionsResolver);
        }

        $this->configureOptions($optionsResolver);

        $optionsResolver->setRequired(['required', 'default_value', 'label', 'groups', 'constraints']);

        $optionsResolver->setAllowedTypes('required', 'bool');
        $optionsResolver->setAllowedTypes('label', ['string', 'null', 'bool']);
        $optionsResolver->setAllowedTypes('groups', 'array');
        $optionsResolver->setAllowedTypes('constraints', 'array');

        // @deprecated Replace with "string[]" allowed type when support for Symfony 2.8 ends
        $optionsResolver->setAllowedValues(
            'groups',
            function (array $groups): bool {
                foreach ($groups as $group) {
                    if (!is_string($group)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $optionsResolver->setAllowedValues(
            'constraints',
            function (array $constraints): bool {
                return $this->validateConstraints($constraints);
            }
        );

        $optionsResolver->setNormalizer(
            'groups',
            function (Options $options, array $value): array {
                if (!$this->parentBuilder instanceof ParameterBuilderInterface) {
                    return $value;
                }

                if (!$this->parentBuilder->getType() instanceof CompoundParameterTypeInterface) {
                    return $value;
                }

                return $this->parentBuilder->getGroups();
            }
        );

        $optionsResolver->setAllowedValues(
            'label',
            function ($value): bool {
                if (!is_bool($value)) {
                    return true;
                }

                return $value === false;
            }
        );

        $resolvedOptions = $optionsResolver->resolve($options);

        $this->isRequired = $resolvedOptions['required'];
        $this->defaultValue = $resolvedOptions['default_value'];
        $this->label = $resolvedOptions['label'];
        $this->groups = $resolvedOptions['groups'];
        $this->constraints = $resolvedOptions['constraints'];

        unset(
            $resolvedOptions['required'],
            $resolvedOptions['default_value'],
            $resolvedOptions['label'],
            $resolvedOptions['groups'],
            $resolvedOptions['constraints']
        );

        return $resolvedOptions;
    }

    /**
     * Validates the list of constraints to be either a Symfony constraint or a closure.
     */
    private function validateConstraints(array $constraints): bool
    {
        foreach ($constraints as $constraint) {
            if (!$constraint instanceof Closure && !$constraint instanceof Constraint) {
                return false;
            }
        }

        return true;
    }
}
