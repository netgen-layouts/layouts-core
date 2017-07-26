<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\BadMethodCallException;
use Netgen\BlockManager\Exception\Parameters\ParameterBuilderException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterBuilder implements ParameterBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    protected $builderFactory;

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
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface $builderFactory
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parentBuilder
     */
    public function __construct(
        ParameterBuilderFactoryInterface $builderFactory,
        $name = null,
        ParameterTypeInterface $type = null,
        array $options = array(),
        ParameterBuilderInterface $parentBuilder = null
    ) {
        $this->builderFactory = $builderFactory;

        $this->name = $name;
        $this->type = $type;
        $this->parentBuilder = $parentBuilder;

        $this->options = $this->resolveOptions($options);
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
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException If the option does not exist
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw ParameterBuilderException::noOption($name, $this->name);
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
    public function setOption($name, $value)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Setting the options is not possible after parameters have been built.');
        }

        $options = $this->options + array(
            'required' => $this->isRequired,
            'default_value' => $this->defaultValue,
            'label' => $this->label,
            'groups' => $this->groups,
        );

        $options[$name] = $value;

        $this->options = $this->resolveOptions($options);

        return $this;
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
        if ($this->locked) {
            throw new BadMethodCallException('Setting the required flag is not possible after parameters have been built.');
        }

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
        if ($this->locked) {
            throw new BadMethodCallException('Setting the default value is not possible after parameters have been built.');
        }

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
        if ($this->locked) {
            throw new BadMethodCallException('Setting the label is not possible after parameters have been built.');
        }

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
        return $this->groups;
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
        if ($this->locked) {
            throw new BadMethodCallException('Setting the groups is not possible after parameters have been built.');
        }

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
            array(
                'name' => $name,
                'type' => $type,
                'options' => $options,
                'parent' => $this,
            )
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
            throw ParameterBuilderException::noParameter($name);
        }

        return $this->unresolvedChildren[$name];
    }

    /**
     * Returns the builders for all parameters, optionally filtered by the group.
     *
     * @param string $group
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface[]
     */
    public function all($group = null)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Accessing parameter builders is not possible after parameters have been built.');
        }

        return array_filter(
            $this->unresolvedChildren,
            function (ParameterBuilderInterface $builder) use ($group) {
                if ($group === null) {
                    return true;
                }

                return in_array($group, $builder->getGroups(), true);
            }
        );
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

        // We build the sub parameters in order to lock the child builders
        $subParameters = $builder->buildParameters();

        if (!$builder->getType() instanceof CompoundParameterTypeInterface) {
            return new Parameter($data);
        }

        $data['parameters'] = $subParameters;

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

        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('label', null);
        $optionsResolver->setDefault('groups', array());

        if ($this->type instanceof ParameterTypeInterface) {
            $this->type->configureOptions($optionsResolver);
        }

        $this->configureOptions($optionsResolver);

        $optionsResolver->setRequired(array('required', 'default_value', 'label', 'groups'));

        $optionsResolver->setAllowedTypes('required', 'bool');
        $optionsResolver->setAllowedTypes('label', array('string', 'null', 'bool'));
        $optionsResolver->setAllowedTypes('groups', 'array');

        $optionsResolver->setNormalizer(
            'groups',
            function (Options $options, $value) {
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
            function ($value) {
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

        unset(
            $resolvedOptions['required'],
            $resolvedOptions['default_value'],
            $resolvedOptions['label'],
            $resolvedOptions['groups']
        );

        return $resolvedOptions;
    }

    /**
     * Configures the parameter options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
    }
}
