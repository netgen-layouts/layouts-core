<?php

namespace Netgen\BlockManager\BlockDefinition\Form\ParameterMapper;

use Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;
use RuntimeException;

class ParameterMapper implements ParameterMapperInterface
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandlerInterface[]
     */
    protected $parameterHandlers = array();

    /**
     * Adds the parameter handler for specific parameter type.
     *
     * @param string $parameterType
     * @param \Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandlerInterface $parameterHandler
     */
    public function addParameterHandler($parameterType, ParameterHandlerInterface $parameterHandler)
    {
        $this->parameterHandlers[$parameterType] = $parameterHandler;
    }

    /**
     * Maps the block definition parameters to form types.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $blockDefinition
     * @param array $parameterNames
     */
    public function mapParameters(FormBuilderInterface $formBuilder, BlockDefinitionInterface $blockDefinition, array $parameterNames = array())
    {
        $parameters = $blockDefinition->getParameters();
        $constraints = $blockDefinition->getParameterConstraints();

        if (empty($parameterNames)) {
            $parameterNames = array_keys($parameters);
        }

        foreach ($parameterNames as $parameterName) {
            if (!isset($this->parameterHandlers[$parameters[$parameterName]->getType()])) {
                throw new RuntimeException("No parameter handler found for '{$parameters[$parameterName]->getType()}' parameter type.");
            }

            $formBuilder->add(
                $parameterName,
                $this->parameterHandlers[$parameters[$parameterName]->getType()]->getFormType(),
                array(
                    'required' => $parameters[$parameterName]->isRequired(),
                    'label' => $parameters[$parameterName]->getName(),
                    'property_path' => 'parameters[' . $parameterName . ']',
                    'constraints' => isset($constraints[$parameterName]) && is_array($constraints[$parameterName]) ?
                        $constraints[$parameterName] :
                        null,
                ) + $this->parameterHandlers[$parameters[$parameterName]->getType()]->convertOptions($parameters[$parameterName])
            );
        }
    }

    /**
     * Maps the block definition parameters to hidden form types.
     *
     * Useful for building inline form types for blocks.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $blockDefinition
     * @param array $parameterNames
     */
    public function mapHiddenParameters(FormBuilderInterface $formBuilder, BlockDefinitionInterface $blockDefinition, array $parameterNames = array())
    {
        $parameters = $blockDefinition->getParameters();
        $constraints = $blockDefinition->getParameterConstraints();

        if (empty($parameterNames)) {
            $parameterNames = array_keys($parameters);
        }

        foreach ($parameterNames as $parameterName) {
            if (!isset($this->parameterHandlers[$parameters[$parameterName]->getType()])) {
                throw new RuntimeException("No parameter handler found for '{$parameters[$parameterName]->getType()}' parameter type.");
            }

            $formBuilder->add(
                $parameterName,
                'hidden',
                array(
                    'required' => $parameters[$parameterName]->isRequired(),
                    'property_path' => 'parameters[' . $parameterName . ']',
                    'constraints' => isset($constraints[$parameterName]) && is_array($constraints[$parameterName]) ?
                        $constraints[$parameterName] :
                        null,
                )
            );
        }
    }
}
