<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

final class RangeMapper extends Mapper
{
    public function getFormType()
    {
        return RangeType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition)
    {
        $options = $parameterDefinition->getOptions();

        return [
            'attr' => [
                'min' => $options['min'],
                'max' => $options['max'],
            ],
        ];
    }
}
