<?php

declare(strict_types=1);

namespace Netgen\Layouts\Validator\Constraint\Structs;

use Netgen\Layouts\Parameters\ParameterDefinitionCollectionInterface;
use Symfony\Component\Validator\Constraint;

final class ParameterStruct extends Constraint
{
    public string $message = 'netgen_layouts.parameter_struct.invalid_value';

    public string $fieldReadOnlyMessage = 'netgen_layouts.parameter_struct.readonly';

    /**
     * Parameter definition collection used to validate parameter values against.
     */
    public ParameterDefinitionCollectionInterface $parameterDefinitions;

    /**
     * If true, missing parameters will pass validation (e.g. when updating the value).
     */
    public bool $allowMissingFields = false;

    /**
     * If true, setting read only fields will NOT pass validation (e.g. when updating the value).
     */
    public bool $checkReadOnlyFields = false;

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return ['parameterDefinitions'];
    }

    public function validatedBy(): string
    {
        return 'nglayouts_parameter_struct';
    }
}
