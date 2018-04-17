<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

final class RangeMapper extends Mapper
{
    public function getFormType()
    {
        return RangeType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
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
