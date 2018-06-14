<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class QueryParameter implements ConditionTypeInterface
{
    public function getType(): string
    {
        return 'query_parameter';
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

    public function matches(Request $request, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (empty($value['parameter_name'])) {
            return false;
        }

        $queryParameters = $request->query;
        if (!$queryParameters->has($value['parameter_name'])) {
            return false;
        }

        $parameterValues = array_map('trim', $value['parameter_values']);

        return empty($value['parameter_values']) || in_array(
            $queryParameters->get($value['parameter_name']),
            $parameterValues,
            true
        );
    }
}
