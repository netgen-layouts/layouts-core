<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Form\KeyValuesType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RouteParameter extends Mapper
{
    public function getFormType()
    {
        return KeyValuesType::class;
    }

    public function getFormOptions()
    {
        return [
            'label' => false,
            'required' => false,
            'key_name' => 'parameter_name',
            'key_label' => 'condition_type.route_parameter.parameter_name',
            'values_name' => 'parameter_values',
            'values_label' => 'condition_type.route_parameter.parameter_values',
            'values_type' => TextType::class,
            'values_options' => [
                'empty_data' => ' ',
            ],
        ];
    }
}
