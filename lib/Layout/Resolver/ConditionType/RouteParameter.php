<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

use function count;
use function in_array;
use function is_array;

final class RouteParameter extends ConditionType
{
    public static function getType(): string
    {
        return 'route_parameter';
    }

    public function getConstraints(): array
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
                            ],
                        ),
                        'parameter_values' => new Constraints\Required(
                            [
                                new Constraints\Type(['type' => 'array']),
                                new Constraints\All(
                                    [
                                        'constraints' => [
                                            new Constraints\Type(['type' => 'scalar']),
                                        ],
                                    ],
                                ),
                            ],
                        ),
                    ],
                ],
            ),
        ];
    }

    public function matches(Request $request, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (($value['parameter_name'] ?? '') === '') {
            return false;
        }

        $routeParameters = $request->attributes->get('_route_params', []);
        if (!isset($routeParameters[$value['parameter_name']])) {
            return false;
        }

        return count($value['parameter_values']) === 0 || in_array(
            $routeParameters[$value['parameter_name']],
            $value['parameter_values'],
            true,
        );
    }
}
