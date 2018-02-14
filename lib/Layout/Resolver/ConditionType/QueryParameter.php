<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class QueryParameter implements ConditionTypeInterface
{
    public function getType()
    {
        return 'query_parameter';
    }

    public function getConstraints()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Collection(
                array(
                    'fields' => array(
                        'parameter_name' => new Constraints\Required(
                            array(
                                new Constraints\NotBlank(),
                                new Constraints\Type(array('type' => 'string')),
                            )
                        ),
                        'parameter_values' => new Constraints\Required(
                            array(
                                new Constraints\Type(array('type' => 'array')),
                                new Constraints\All(
                                    array(
                                        'constraints' => array(
                                            new Constraints\Type(array('type' => 'scalar')),
                                        ),
                                    )
                                ),
                            )
                        ),
                    ),
                )
            ),
        );
    }

    public function matches(Request $request, $value)
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
