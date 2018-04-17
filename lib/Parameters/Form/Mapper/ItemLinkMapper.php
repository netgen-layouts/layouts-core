<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\ItemLinkDataMapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserDynamicType;
use Symfony\Component\Form\FormBuilderInterface;

final class ItemLinkMapper extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserDynamicType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return [
            'item_types' => $parameterDefinition->getOption('value_types'),
        ];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinitionInterface $parameterDefinition)
    {
        $form->setDataMapper(new ItemLinkDataMapper());
    }
}
