<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use RuntimeException;

class FormMapper implements FormMapperInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface[]
     */
    protected $parameterHandlers = array();

    /**
     * Adds the parameter handler for specific parameter type.
     *
     * @param string $parameterType
     * @param \Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface $parameterHandler
     */
    public function addParameterHandler($parameterType, ParameterHandlerInterface $parameterHandler)
    {
        $this->parameterHandlers[$parameterType] = $parameterHandler;
    }

    /**
     * Maps the parameter to form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param string $labelPrefix
     * @param string $propertyPathPrefix
     */
    public function mapParameter(
        FormBuilderInterface $formBuilder,
        ParameterInterface $parameter,
        $parameterName,
        $labelPrefix,
        $propertyPathPrefix = 'parameters'
    ) {
        $parameterType = $parameter->getType();

        if (!isset($this->parameterHandlers[$parameterType])) {
            throw new RuntimeException("No parameter handler found for '{$parameterType}' parameter type.");
        }

        $formBuilder->add(
            $parameterName,
            $this->parameterHandlers[$parameterType]->getFormType(),
            array(
                'required' => $parameter->isRequired(),
                'label' => $labelPrefix . '.' . $parameterName,
                'translation_domain' => 'ngbm_parameters',
                'property_path' => $this->getPropertyPath($parameterName, $propertyPathPrefix),
                'constraints' => $parameter->getConstraints(),
            ) + $this->parameterHandlers[$parameterType]->convertOptions($parameter)
        );
    }

    /**
     * Maps the parameter to hidden form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param string $propertyPathPrefix
     */
    public function mapHiddenParameter(
        FormBuilderInterface $formBuilder,
        ParameterInterface $parameter,
        $parameterName,
        $propertyPathPrefix = 'parameters'
    ) {
        $formBuilder->add(
            $parameterName,
            'hidden',
            array(
                'required' => $parameter->isRequired(),
                'property_path' => $this->getPropertyPath($parameterName, $propertyPathPrefix),
                'constraints' => $parameter->getConstraints(),
            )
        );
    }

    /**
     * Returns the property path based on parameter name and prefix.
     *
     * @param string $parameterName
     * @param string $propertyPathPrefix
     *
     * @return string
     */
    protected function getPropertyPath($parameterName, $propertyPathPrefix)
    {
        if (empty($propertyPathPrefix)) {
            return $parameterName;
        }

        return $propertyPathPrefix . '[' . $parameterName . ']';
    }
}
