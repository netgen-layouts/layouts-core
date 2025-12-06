<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class RuleGroup extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        public bool $allowInvalid = false,
        public string $message = 'netgen_layouts.rule_group.rule_group_not_found',
        public string $invalidFormatMessage = 'netgen_layouts.rule_group.invalid_format',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_rule_group';
    }
}
