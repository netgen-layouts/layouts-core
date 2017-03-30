<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\BadMethodCallException;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterBuilder implements ParameterBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $isRequired;

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    protected $parentBuilder;

    /**
     * @var array
     */
    protected $unresolvedChildren = array();

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $resolvedChildren = array();

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface $parameterTypeRegistry
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parentBuilder
     */
    public function __construct(
        ParameterTypeRegistryInterface $parameterTypeRegistry,
        $name = null,
        ParameterTypeInterface $type = null,
        array $options = array(),
        ParameterBuilderInterface $parentBuilder = null
    ) {
        $this->parameterTypeRegistry = $parameterTypeRegistry;

        $this->name = $name;
        $this->type = $type;
        $this->options = $this->resolveOptions($options);

        $this->isRequired = $this->options['required'];
        $this->defaultValue = $this->options['default_value'];
        $this->label = $this->options['label'];
        $this->groups = $this->options['groups'];

        unset(
            $this->options['required'],
            $this->options['default_value'],
            $this->options['label'],
            $this->options['groups']
        );

        $this->parentBuilder = $parentBuilder;
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parameter type.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the parameter option with provided name.
     *
     * @param string $name
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If the option does not exist
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(
                'name',
                sprintf(
                    'Option "%s" does not exist in builder for "%s" parameter',
                    $name,
                    $this->name
                )
            );
        }

        return $this->options[$name];
    }

    /**
     * Returns if the parameter option with provided name exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Sets if the parameter is required.
     *
     * @param bool $isRequired
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setRequired($isRequired)
    {
        $this->isRequired = (bool) $isRequired;

        return $this;
    }

    /**
     * Returns the default value of the parameter.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Sets the default value of the parameter.
     *
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * Returns the parameter label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the parameter label.
     *
     * @param string $label
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns the parameter groups.
     *
     * @return array
     */
    public function getGroups()
    {
        if (!$this->parentBuilder instanceof ParameterBuilderInterface) {
            return $this->groups;
        }

        if (!$this->parentBuilder->getType() instanceof CompoundParameterTypeInterface) {
            return $this->groups;
        }

        // Child parameters receive the group from the parent
        return $this->parentBuilder->getGroups();
    }

    /**
     * Sets the parameter groups.
     *
     * @param array $groups
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Adds the parameter to the builder.
     *
     * @param string $name
     * @param string $type
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function add($name, $type, array $options = array())
    {
        if ($this->locked) {
            throw new BadMethodCallException('Parameters cannot be added after they have been built.');
        }

        $type = $this->parameterTypeRegistry->getParameterTypeByClass($type);

        if ($this->type instanceof CompoundParameterTypeInterface && $type instanceof CompoundParameterTypeInterface) {
            throw new InvalidArgumentException(
                'name',
                'Compound parameters cannot be added to compound parameters.'
            );
        }

        if ($this->type !== null && !$this->type instanceof CompoundParameterTypeInterface) {
            throw new InvalidArgumentException(
                'name',
                'Parameters cannot be added to non-compound parameters.'
            );
        }

        $this->unresolvedChildren[$name] = new self(
            $this->parameterTypeRegistry,
            $name,
            $type,
            $options,
            $this
        );

        return $this;
    }

    /**
     * Returns the builder for parameter with provided name.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function get($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Accessing parameter builders is not possible after parameters have been built.');
        }

        if (!$this->has($name)) {
            throw new InvalidArgumentException(
                'name',
                sprintf(
                    'Parameter with "%s" name does not exist in the builder.',
                    $name
                )
            );
        }

        return $this->unresolvedChildren[$name];
    }

    /**
     * Returns if the builder has the parameter with provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->unresolvedChildren[$name]);
    }

    /**
     * Removes the parameter from the builder.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function remove($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Removing parameters is not possible after parameters have been built.');
        }

        unset($this->unresolvedChildren[$name]);

        return $this;
    }

    /**
     * Returns the count of the parameters.
     *
     * @return int
     */
    public function count()
    {
        return count($this->unresolvedChildren);
    }

    /**
     * Builds the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function buildParameters()
    {
        if ($this->locked) {
            return $this->resolvedChildren;
        }

        if ($this->type instanceof CompoundParameterTypeInterface) {
            $this->type->buildParameters($this);
        }

        foreach ($this->unresolvedChildren as $name => $builder) {
            $this->resolvedChildren[$name] = $this->buildParameter($builder);
        }

        $this->locked = true;

        return $this->resolvedChildren;
    }

    /**
     * Builds the parameter.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    protected function buildParameter(ParameterBuilderInterface $builder)
    {
        $data = array(
            'name' => $builder->getName(),
            'type' => $builder->getType(),
            'options' => $builder->getOptions(),
            'isRequired' => $builder->isRequired(),
            'defaultValue' => $builder->getDefaultValue(),
            'label' => $builder->getLabel(),
            'groups' => $builder->getGroups(),
        );

        if (!$builder->getType() instanceof CompoundParameterTypeInterface) {
            return new Parameter($data);
        }

        $data['parameters'] = $builder->buildParameters();

        return new CompoundParameter($data);
    }

    /**
     * Resolves the parameter options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function resolveOptions(array $options)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);

        return $optionsResolver->resolve($options);
    }

    /**
     * Configures the parameter options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('label', null);
        $optionsResolver->setDefault('groups', array());

        if ($this->type instanceof ParameterTypeInterface) {
            $this->type->configureOptions($optionsResolver);
        }

        $optionsResolver->setRequired(array('required', 'default_value', 'label', 'groups'));

        $optionsResolver->setAllowedTypes('required', 'bool');
        $optionsResolver->setAllowedTypes('label', array('string', 'null', 'bool'));
        $optionsResolver->setAllowedTypes('groups', 'array');

        $optionsResolver->setAllowedValues(
            'label',
            function ($value) {
                if (!is_bool($value)) {
                    return true;
                }

                return $value === false;
            }
        );
    }
}
