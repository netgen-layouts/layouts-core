<?php

namespace Netgen\BlockManager\BlockDefinition\Form\ParameterMapper;

use Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface ParameterMapperInterface
{
    /**
     * Maps the block definition parameters to form types.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $blockDefinition
     */
    public function mapParameters(FormBuilderInterface $formBuilder, BlockDefinitionInterface $blockDefinition);

    /**
     * Maps the block definition parameters to hidden form types.
     *
     * Useful for building inline form types for blocks.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\BlockDefinition\BlockDefinitionInterface $blockDefinition
     * @param array $parameterNames
     */
    public function mapHiddenParameters(FormBuilderInterface $formBuilder, BlockDefinitionInterface $blockDefinition, array $parameterNames = array());
}
