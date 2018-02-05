<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberMapper extends Mapper
{
    public function getFormType()
    {
        return NumberType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return array(
            'scale' => $parameterDefinition->getOption('scale'),
        );
    }
}
