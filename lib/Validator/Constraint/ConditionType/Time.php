<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint\ConditionType;

use Symfony\Component\Validator\Constraint;

final class Time extends Constraint
{
    /**
     * @var string
     */
    public $toLowerThanFromMessage = 'netgen_block_manager.layout_resolver.condition_type.time.to_lower';

    public function validatedBy(): string
    {
        return 'ngbm_condition_type_time';
    }
}
