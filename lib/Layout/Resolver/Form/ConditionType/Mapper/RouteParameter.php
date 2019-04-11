<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Form\KeyValuesType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class RouteParameter extends Mapper
{
    public function getFormType(): string
    {
        return KeyValuesType::class;
    }

    public function getFormOptions(): array
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
