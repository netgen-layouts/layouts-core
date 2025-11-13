<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Structs;

use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

final class ParameterStruct extends Constraint
{
    #[HasNamedArguments]
    public function __construct(
        /**
         * Parameter definition collection used to validate parameter values against.
         */
        public ParameterDefinitionCollectionInterface $parameterDefinitions,
        /**
         * If true, missing parameters will pass validation (e.g. when updating the value).
         */
        public bool $allowMissingFields = false,
        /**
         * If true, setting read only fields will NOT pass validation (e.g. when updating the value).
         */
        public bool $checkReadOnlyFields = false,
        public string $message = 'netgen_layouts.parameter_struct.invalid_value',
        public string $fieldReadOnlyMessage = 'netgen_layouts.parameter_struct.readonly',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct(null, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return 'nglayouts_parameter_struct';
    }
}
