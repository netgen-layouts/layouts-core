<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use RuntimeException;

class FormMapper implements FormMapperInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface[]
     */
    protected $parameterHandlers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface[] $parameterHandlers
     */
    public function __construct(array $parameterHandlers = array())
    {
        foreach ($parameterHandlers as $parameterHandler) {
            if (!$parameterHandler instanceof ParameterHandlerInterface) {
                throw new RuntimeException(
                    sprintf(
                        'Parameter handler "%s" needs to implement ParameterHandlerInterface.',
                        get_class($parameterHandler)
                    )
                );
            }
        }

        $this->parameterHandlers = $parameterHandlers;
    }

    /**
     * Maps the parameter to form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $options
     */
    public function mapParameter(
        FormBuilderInterface $formBuilder,
        ParameterInterface $parameter,
        $parameterName,
        array $options = array()
    ) {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $options = $optionsResolver->resolve($options);

        $parameterType = $parameter->getType();

        if (!isset($this->parameterHandlers[$parameterType])) {
            throw new RuntimeException("No parameter handler found for '{$parameterType}' parameter type.");
        }

        $parameterHandler = $this->parameterHandlers[$parameterType];

        $formBuilder->add(
            $parameterName,
            $parameterHandler->getFormType(),
            $parameterHandler->convertOptions($parameter) + $parameterHandler->getDefaultOptions(
                $parameter, $parameterName, $options
            )
        );
    }

    /**
     * Configures the form mapper options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('label_prefix', false);
        $optionsResolver->setDefault('property_path_prefix', 'parameters');
        $optionsResolver->setDefault('parameter_validation_groups', null);

        $optionsResolver->setRequired(array('label_prefix', 'property_path_prefix', 'parameter_validation_groups'));

        $optionsResolver->setAllowedTypes('label_prefix', 'string');
        $optionsResolver->setAllowedTypes('property_path_prefix', 'string');
        $optionsResolver->setAllowedTypes('parameter_validation_groups', array('null', 'array'));
    }
}
