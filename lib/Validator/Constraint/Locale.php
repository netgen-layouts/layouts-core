<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Locale extends Constraint
{
    public $message = 'netgen_block_manager.locale.invalid_locale';

    public function validatedBy(): string
    {
        return 'ngbm_locale';
    }
}
