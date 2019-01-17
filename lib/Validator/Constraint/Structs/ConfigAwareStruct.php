<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Validator\Constraint\Structs;

use Symfony\Component\Validator\Constraint;

final class ConfigAwareStruct extends Constraint
{
    /**
     * @var string
     */
    public $noConfigDefinitionMessage = 'netgen_block_manager.config_aware_struct.missing_definition';

    /**
     * If true, missing parameters will pass validation (e.g. when updating the value).
     *
     * @var bool
     */
    public $allowMissingFields = false;

    public function validatedBy(): string
    {
        return 'ngbm_config_aware_struct';
    }
}
