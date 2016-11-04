<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;

class LinkMapper extends Mapper
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
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $formOptions
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter, $parameterName, array $formOptions)
    {
        $valueTypes = $parameter->getOptions()['value_types'];

        return array(
            'label' => false,
            'value_types' => !empty($valueTypes) ? $valueTypes : $this->defaultValueTypes,
        );
    }

    /**
     * Allows the mapper to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterInterface $parameter, FormBuilderInterface $form)
    {
        parent::handleForm($parameter, $form);

        $form->setDataMapper(new LinkDataMapper($this->parameterType));
    }
}
