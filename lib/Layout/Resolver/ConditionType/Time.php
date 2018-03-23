<?php

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
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
        return array(
            new Constraints\NotBlank(),
            new Constraints\Collection(
                array(
                    'fields' => array(
                        'from' => new Constraints\Required(
                            array(
                                new DateTimeConstraint(),
                            )
                        ),
                        'to' => new Constraints\Required(
                            array(
                                new DateTimeConstraint(),
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

        if (empty($value['from']) && empty($value['to'])) {
            return true;
        }

        $currentTime = DateTimeImmutable::createFromFormat('U', time());

        $visibleFrom = isset($value['from']) ? $this->createDateTime($value['from']) : null;
        $visibleTo = isset($value['to']) ? $this->createDateTime($value['to']) : null;

        if ($visibleFrom instanceof DateTimeInterface && $currentTime < $visibleFrom) {
            return false;
        }

        if ($visibleTo instanceof DateTimeInterface && $currentTime > $visibleTo) {
            return false;
        }

        return true;
    }

    private function createDateTime($value)
    {
        if (!is_array($value) || empty($value['datetime']) || empty($value['timezone'])) {
            return null;
        }

        return new DateTimeImmutable($value['datetime'], new DateTimeZone($value['timezone']));
    }
}
