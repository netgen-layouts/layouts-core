<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class ConfigAwareStruct extends Constraint
{
    public string $noConfigDefinitionMessage = 'netgen_layouts.config_aware_struct.missing_definition';

    /**
     * If true, missing parameters will pass validation (e.g. when updating the value).
     */
    public bool $allowMissingFields = false;

    public function validatedBy(): string
    {
        return 'nglayouts_config_aware_struct';
    }
}
