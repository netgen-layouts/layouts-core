<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterBuilderFactory implements ParameterBuilderFactoryInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface
     */
    protected $parameterTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterTypeRegistryInterface $parameterTypeRegistry
     */
    public function __construct(ParameterTypeRegistryInterface $parameterTypeRegistry)
    {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
    }

    /**
     * Returns the new instance of parameter builder.
     *
     * @param array $config
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function createParameterBuilder(array $config = array())
    {
        $config = $this->resolveOptions($config);

        $parameterBuilder = new ParameterBuilder(
            $this,
            $config['name'],
            $config['type'],
            $config['options'],
            $config['parent']
        );

        return $parameterBuilder;
    }

    /**
     * Resolves the provided parameter builder configuration.
     *
     * @param array $config
     *
     * @return array
     */
    protected function resolveOptions(array $config)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefault('name', null);
        $optionsResolver->setDefault('type', null);
        $optionsResolver->setDefault('options', array());
        $optionsResolver->setDefault('parent', null);

        $optionsResolver->setRequired(array('name', 'type', 'options', 'parent'));

        $optionsResolver->setAllowedTypes('name', array('null', 'string'));
        $optionsResolver->setAllowedTypes('type', array('null', 'string', ParameterTypeInterface::class));
        $optionsResolver->setAllowedTypes('options', 'array');
        $optionsResolver->setAllowedTypes('parent', array('null', ParameterBuilderInterface::class));

        $optionsResolver->setNormalizer(
            'type',
            function (Options $options, $value) {
                if (!is_string($value)) {
                    return $value;
                }

                return $this->parameterTypeRegistry->getParameterTypeByClass($value);
            }
        );

        $config = $optionsResolver->resolve($config);

        return $config;
    }
}
