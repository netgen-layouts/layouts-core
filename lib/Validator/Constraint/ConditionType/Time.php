<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\ConditionType;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class Time extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        public string $toLowerThanFromMessage = 'netgen_layouts.layout_resolver.condition_type.time.to_lower',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_condition_type_time';
    }
}
