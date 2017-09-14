<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Symfony\Component\Form\FormBuilderInterface;

class ItemLinkMapper extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserDynamicType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'item_types' => $parameter->getOption('value_types'),
        );
    }

    public function handleForm(FormBuilderInterface $form, ParameterInterface $parameter)
    {
        $form->setDataMapper(new ItemLinkDataMapper());
    }
}
