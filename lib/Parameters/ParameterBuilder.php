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
    protected $options = array();

    /**
     * @var bool
     */
    protected $isRequired = false;

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
    protected $groups = array();

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    protected $parentBuilder;

    /**
     * @var array
     */
    protected $unresolvedChildren = array();

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
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

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw ParameterBuilderException::noOption($name, $this->name);
        }

        return $this->options[$name];
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

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

    public function isRequired()
    {
        return $this->isRequired;
    }

    public function setRequired($isRequired)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Setting the required flag is not possible after parameters have been built.');
        }

        $this->isRequired = (bool) $isRequired;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
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

    public function setLabel($label)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Setting the label is not possible after parameters have been built.');
        }

        $this->label = $label;

        return $this;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups(array $groups)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Setting the groups is not possible after parameters have been built.');
        }

        $this->groups = $groups;

        return $this;
    }

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

    public function has($name)
    {
        return isset($this->unresolvedChildren[$name]);
    }

    public function remove($name)
    {
        if ($this->locked) {
            throw new BadMethodCallException('Removing parameters is not possible after parameters have been built.');
        }

        unset($this->unresolvedChildren[$name]);

        return $this;
    }

    public function count()
    {
        return count($this->unresolvedChildren);
    }

    public function buildParameterDefinitions()
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
     * Builds the parameter definition.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface
     */
    protected function buildParameterDefinition(ParameterBuilderInterface $builder)
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
        $subParameters = $builder->buildParameterDefinitions();

        if (!$builder->getType() instanceof CompoundParameterTypeInterface) {
            return new ParameterDefinition($data);
        }

        $data['parameterDefinitions'] = $subParameters;

        return new CompoundParameterDefinition($data);
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
