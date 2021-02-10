<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\ConditionType;

use Symfony\Component\Validator\Constraint;

final class Time extends Constraint
{
    public string $toLowerThanFromMessage = 'netgen_layouts.layout_resolver.condition_type.time.to_lower';

    public function validatedBy(): string
    {
        return 'nglayouts_condition_type_time';
    }
}
