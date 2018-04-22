<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an identifier.
 *
 * An identifier is a string starting with a letter, followed by any
 * combination of letters, numbers and underscores.
 */
final class IdentifierType extends ParameterType
{
    public function getIdentifier()
    {
        return 'identifier';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        return [
            new Constraints\Type(['type' => 'string']),
            new Constraints\Regex(
                [
                    'pattern' => '/^[A-Za-z]([A-Za-z0-9_])*$/',
                ]
            ),
        ];
    }
}
