<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Exception implements ConditionTypeInterface
{
    public function getType()
    {
        return 'exception';
    }

    public function getConstraints()
    {
        return array(
            new Constraints\NotNull(),
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(
                array(
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'int')),
                        new Constraints\GreaterThanOrEqual(array('value' => 400)),
                        new Constraints\LessThan(array('value' => 600)),
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

        if (!$request->attributes->has('exception')) {
            return false;
        }

        $exception = $request->attributes->get('exception');
        if (!$exception instanceof FlattenException) {
            return false;
        }

        return empty($value) || in_array($exception->getStatusCode(), $value, true);
    }
}
