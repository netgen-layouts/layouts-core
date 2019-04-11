<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an identifier.
 *
 * An identifier is a string starting with a letter, followed by any
 * combination of letters, numbers and underscores.
 */
final class IdentifierType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'identifier';
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, $value): bool
    {
        return $value === null || $value === '';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'string']),
            new Constraints\Regex(['pattern' => '/^[A-Za-z]([A-Za-z0-9_])*$/']),
        ];
    }
}
