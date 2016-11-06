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
     * @var array
     */
    protected $unresolvedParameters = array();

    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $resolvedParameters = array();

    /**
     * @var array
     */
    protected $overrideOptions = array();

    /**
     * @var bool
     */
    protected $locked = false;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface $parameterTypeRegistry
     * @param array $overrideOptions
     */
    public function __construct(ParameterTypeRegistryInterface $parameterTypeRegistry, array $overrideOptions = array())
    {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
        $this->overrideOptions = $overrideOptions;
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

        $this->unresolvedParameters[$name] = array(
            'type' => $type,
            'options' => $this->overrideOptions + $options,
        );

        if ($type instanceof CompoundParameterTypeInterface) {
            $childBuilder = new self(
                $this->parameterTypeRegistry,
                array(
                    // Child parameters receive the group from the parent
                    'groups' => isset($options['groups']) ? $options['groups'] : array(),
                )
            );

            $type->buildParameters($childBuilder);

            $this->unresolvedParameters[$name]['builder'] = $childBuilder;
        }

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

        if (!$this->unresolvedParameters[$name]['type'] instanceof CompoundParameterTypeInterface) {
            throw new InvalidArgumentException(
                'name',
                sprintf(
                    'Parameter with "%s" name is not compound.',
                    $name
                )
            );
        }

        return $this->unresolvedParameters[$name]['builder'];
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
        return isset($this->unresolvedParameters[$name]);
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

        unset($this->unresolvedParameters[$name]);

        return $this;
    }

    /**
     * Returns the count of the parameters.
     *
     * @return int
     */
    public function count()
    {
        return count($this->unresolvedParameters);
    }

    /**
     * Builds the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function buildParameters()
    {
        if ($this->locked) {
            return $this->resolvedParameters;
        }

        foreach ($this->unresolvedParameters as $parameterName => $parameterOptions) {
            $parameterType = $parameterOptions['type'];

            $childParameters = array();
            if (isset($parameterOptions['builder'])) {
                $childParameters = $parameterOptions['builder']->buildParameters();
            }

            $resolvedOptions = $this->resolveOptions($parameterType, $parameterOptions['options']);

            $this->resolvedParameters[$parameterName] = $this->buildParameter(
                $parameterName,
                $parameterType,
                $resolvedOptions,
                $childParameters
            );
        }

        $this->locked = true;

        return $this->resolvedParameters;
    }

    /**
     * Builds the parameter.
     *
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $childParameters
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    protected function buildParameter(
        $name,
        ParameterTypeInterface $type,
        array $options = array(),
        array $childParameters = array()
    ) {
        if (!empty($childParameters)) {
            return new CompoundParameter($name, $type, $options, $childParameters);
        }

        return new Parameter($name, $type, $options);
    }

    /**
     * Resolves the parameter options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     * @param array $options
     *
     * @return array
     */
    protected function resolveOptions(ParameterTypeInterface $parameterType, array $options = array())
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('groups', array());

        $parameterType->configureOptions($optionsResolver);

        $optionsResolver->setRequired(array('required', 'default_value', 'groups'));

        $optionsResolver->setAllowedTypes('required', 'bool');
        $optionsResolver->setAllowedTypes('groups', 'array');

        return $optionsResolver->resolve($options);
    }
}
