<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Utils\DateTimeUtils;
use Netgen\BlockManager\Validator\Constraint\DateTime as DateTimeConstraint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Time implements ConditionTypeInterface
{
    public function getType()
    {
        return 'time';
    }

    public function getConstraints()
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Collection(
                [
                    'fields' => [
                        'from' => new Constraints\Required(
                            [
                                new DateTimeConstraint(),
                            ]
                        ),
                        'to' => new Constraints\Required(
                            [
                                new DateTimeConstraint(),
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

        if (empty($value['from']) && empty($value['to'])) {
            return true;
        }

        $visibleFrom = isset($value['from']) && is_array($value['from']) ?
            DateTimeUtils::createFromArray($value['from']) :
            null;

        $visibleTo = isset($value['to']) && is_array($value['to']) ?
            DateTimeUtils::createFromArray($value['to']) :
            null;

        return DateTimeUtils::isBetweenDates(null, $visibleFrom, $visibleTo);
    }
}
