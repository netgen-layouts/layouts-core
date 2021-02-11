<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Stubs;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType as BaseParameterType;
use Symfony\Component\Validator\Constraints\NotNull;

final class ParameterTypeWithExportImport extends BaseParameterType
{
    public static function getIdentifier(): string
    {
        return 'type';
    }

    public function import(ParameterDefinition $parameterDefinition, $value): string
    {
        return 'import_value';
    }

    public function export(ParameterDefinition $parameterDefinition, $value): string
    {
        return 'export_value';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [new NotNull()];
    }
}
