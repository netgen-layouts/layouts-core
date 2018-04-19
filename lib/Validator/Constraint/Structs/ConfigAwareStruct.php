<?php

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class ConfigAwareStruct extends Constraint
{
    public $noConfigDefinitionMessage = 'netgen_block_manager.config_aware_struct.missing_definition';

    /**
     * If true, missing parameters will pass validation (e.g. when updating the value).
     *
     * @var bool
     */
    public $allowMissingFields = false;

    public function validatedBy()
    {
        return 'ngbm_config_aware_struct';
    }
}
