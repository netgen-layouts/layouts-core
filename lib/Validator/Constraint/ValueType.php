<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class ValueType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'netgen_layouts.value_type.no_value_type';

    public function validatedBy(): string
    {
        return 'nglayouts_value_type';
    }
}
