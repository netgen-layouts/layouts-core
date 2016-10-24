<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Netgen\BlockManager\Exception\RuntimeException;

class FormMapper implements FormMapperInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface
     */
    protected $parameterFilterRegistry;

    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface[]
     */
    protected $parameterHandlers = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\Registry\ParameterFilterRegistryInterface $parameterFilterRegistry
     * @param \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface[] $parameterHandlers
     */
    public function __construct(
        ParameterFilterRegistryInterface $parameterFilterRegistry,
        array $parameterHandlers = array()
    ) {
        $this->parameterFilterRegistry = $parameterFilterRegistry;

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
            $parameterHandler->convertOptions($parameter) + $parameterHandler->getDefaultOptions(
                $parameter, $parameterName, $options
            )
        );

        $parameterHandler->processForm($parameter, $parameterForm);

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
