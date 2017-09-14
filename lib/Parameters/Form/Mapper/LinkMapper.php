<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\LinkDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\LinkType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

class LinkMapper extends Mapper
{
    public function getFormType()
    {
        return LinkType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'label' => false,
            'value_types' => $parameter->getOption('value_types'),
        );
    }

    public function handleForm(FormBuilderInterface $form, ParameterInterface $parameter)
    {
        $form->setDataMapper(new LinkDataMapper($parameter));
    }
}
