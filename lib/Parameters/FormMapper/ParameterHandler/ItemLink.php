<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserDynamicType;
use Netgen\BlockManager\Parameters\Form\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

class ItemLink extends ParameterHandler
{
    /**
     * @var array
     */
    protected $defaultValueTypes;

    /**
     * Constructor.
     *
     * @param array $defaultValueTypes
     */
    public function __construct(array $defaultValueTypes = array())
    {
        $this->defaultValueTypes = $defaultValueTypes;
    }

    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return ContentBrowserDynamicType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter)
    {
        $valueTypes = $parameter->getOptions()['value_types'];

        return array(
            'item_types' => !empty($valueTypes) ? $valueTypes : $this->defaultValueTypes,
        );
    }

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterInterface $parameter, FormBuilderInterface $form)
    {
        $form->setDataMapper(new ItemLinkDataMapper());
    }
}
