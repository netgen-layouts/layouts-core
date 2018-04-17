<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class RouteParameter implements ConditionTypeInterface
{
    public function getType()
    {
        return 'route_parameter';
    }

    public function getConstraints()
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Collection(
                [
                    'fields' => [
                        'parameter_name' => new Constraints\Required(
                            [
                                new Constraints\NotBlank(),
                                new Constraints\Type(['type' => 'string']),
                            ]
                        ),
                        'parameter_values' => new Constraints\Required(
                            [
                                new Constraints\Type(['type' => 'array']),
                                new Constraints\All(
                                    [
                                        'constraints' => [
                                            new Constraints\Type(['type' => 'scalar']),
                                        ],
                                    ]
                                ),
                            ]
                        ),
                    ],
                ]
            ),
        ];
    }

    public function matches(Request $request, $value)
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value['parameter_name'])) {
            return false;
        }

        $routeParameters = $request->attributes->get('_route_params', []);
        if (!isset($routeParameters[$value['parameter_name']])) {
            return false;
        }

        return empty($value['parameter_values']) || in_array(
            $routeParameters[$value['parameter_name']],
            $value['parameter_values'],
            true
        );
    }
}
