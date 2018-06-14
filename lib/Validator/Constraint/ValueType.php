<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ValueType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_block_manager.value_type.no_value_type';

    public function validatedBy(): string
    {
        return 'ngbm_value_type';
    }
}
