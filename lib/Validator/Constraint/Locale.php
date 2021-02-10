<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

final class Locale extends Constraint
{
    public string $message = 'netgen_layouts.locale.invalid_locale';

    public function validatedBy(): string
    {
        return 'nglayouts_locale';
    }
}
