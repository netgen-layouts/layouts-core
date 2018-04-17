<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class LinkMapper extends Mapper
{
    public function getFormType()
    {
        return LinkType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return [
            'label' => false,
            'value_types' => $parameterDefinition->getOption('value_types'),
        ];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinitionInterface $parameterDefinition)
    {
        $form->setDataMapper(new LinkDataMapper($parameterDefinition));
    }
}
