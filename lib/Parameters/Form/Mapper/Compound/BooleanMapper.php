<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper\Compound;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;

final class BooleanMapper extends Mapper
{
    public function getFormType()
    {
        return CompoundBooleanType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return array(
            'mapped' => false,
            'reverse' => $parameterDefinition->getOption('reverse'),
        );
    }
}
