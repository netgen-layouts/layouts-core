<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Structs;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class ConfigAwareStruct extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        /**
         * If true, missing parameters will pass validation (e.g. when updating the value).
         */
        public bool $allowMissingFields = false,
        public string $noConfigDefinitionMessage = 'netgen_layouts.config_aware_struct.missing_definition',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_config_aware_struct';
    }
}
