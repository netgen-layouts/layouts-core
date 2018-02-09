<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DataMapper\DateTimeDataMapper;
use Netgen\BlockManager\Parameters\Form\Type\DateTimeType;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class DateTimeMapper extends Mapper
{
    public function getFormType()
    {
        return DateTimeType::class;
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinitionInterface $parameterDefinition)
    {
        $form->setDataMapper(new DateTimeDataMapper());
    }
}
