<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\Form\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\Form\LinkType;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

class Link extends ParameterHandler
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    protected $parameterType;

    /**
     * @var array
     */
    protected $defaultValueTypes;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $parameterType
     * @param array $defaultValueTypes
     */
    public function __construct(ParameterTypeInterface $parameterType, array $defaultValueTypes = array())
    {
        $this->parameterType = $parameterType;
        $this->defaultValueTypes = $defaultValueTypes;
    }

    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return LinkType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function convertOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        $valueTypes = $parameterDefinition->getOptions()['value_types'];

        return array(
            'value_types' => !empty($valueTypes) ? $valueTypes : $this->defaultValueTypes,
        );
    }

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterDefinitionInterface $parameterDefinition, $parameterName, array $options)
    {
        return array(
            'label' => false,
        ) + parent::getDefaultOptions($parameterDefinition, $parameterName, $options);
    }

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterDefinitionInterface $parameterDefinition, FormBuilderInterface $form)
    {
        parent::handleForm($parameterDefinition, $form);

        $form->setDataMapper(new LinkDataMapper($this->parameterType));
    }
}
