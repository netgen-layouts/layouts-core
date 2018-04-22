<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

final class NumberMapper extends Mapper
{
    public function getFormType()
    {
        return NumberType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition)
    {
        return [
            'scale' => $parameterDefinition->getOption('scale'),
        ];
    }
}
