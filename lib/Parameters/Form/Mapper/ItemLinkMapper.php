<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Symfony\Component\Form\FormBuilderInterface;

class ItemLinkMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface
     */
    protected $valueTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Registry\ValueTypeRegistryInterface $valueTypeRegistry
     */
    public function __construct(ValueTypeRegistryInterface $valueTypeRegistry)
    {
        $this->valueTypeRegistry = $valueTypeRegistry;
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
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter)
    {
        $valueTypes = $parameter->getOption('value_types');

        return array(
            'item_types' => !empty($valueTypes) ?
                $valueTypes :
                array_keys(
                    $this->valueTypeRegistry->getValueTypes()
                ),
        );
    }

    /**
     * Allows the mapper to do any kind of processing to created form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     */
    public function handleForm(FormBuilderInterface $form, ParameterInterface $parameter)
    {
        $form->setDataMapper(new ItemLinkDataMapper());
    }
}
