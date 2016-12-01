<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\BlockManager\Form\KeyValuesType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RouteParameter extends Mapper
{
    /**
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType()
    {
        return KeyValuesType::class;
    }

    /**
     * Maps the form type options from provided target type.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     *
     * @return array
     */
    public function mapOptions(ConditionTypeInterface $conditionType)
    {
        return array(
            'label' => false,
            'key_name' => 'parameter_name',
            'key_label' => 'layout_resolver.condition.route_parameter.parameter_name',
            'values_name' => 'parameter_values',
            'values_label' => 'layout_resolver.condition.route_parameter.parameter_values',
            'values_type' => TextType::class,
        );
    }
}
