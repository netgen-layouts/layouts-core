<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Netgen\BlockManager\Exception\RuntimeException;

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
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param string $parameterName
     * @param array $options
     */
    public function mapParameter(
        FormBuilderInterface $formBuilder,
        ParameterDefinitionInterface $parameterDefinition,
        $parameterName,
        array $options = array()
    ) {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $options = $optionsResolver->resolve($options);

        $parameterType = $parameterDefinition->getType();

        if (!isset($this->parameterHandlers[$parameterType])) {
            throw new RuntimeException(
                sprintf(
                    'No parameter handler found for "%s" parameter type.',
                    $parameterType
                )
            );
        }

        $parameterHandler = $this->parameterHandlers[$parameterType];

        $parameterForm = $formBuilder->create(
            $parameterName,
            $parameterHandler->getFormType(),
            $parameterHandler->convertOptions($parameterDefinition) + $parameterHandler->getDefaultOptions(
                $parameterDefinition, $parameterName, $options
            )
        );

        $parameterHandler->handleForm($parameterDefinition, $parameterForm);

        $formBuilder->add($parameterForm);
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

        $optionsResolver->setRequired(array('label_prefix', 'property_path_prefix'));

        $optionsResolver->setAllowedTypes('label_prefix', 'string');
        $optionsResolver->setAllowedTypes('property_path_prefix', 'string');
    }
}
