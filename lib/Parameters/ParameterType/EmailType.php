<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an e-mail address.
 */
final class EmailType extends ParameterType
{
    public function getIdentifier()
    {
        return 'email';
    }

    protected function getValueConstraints(ParameterDefinitionInterface $parameterDefinition, $value)
    {
        return [
            new Constraints\Type(['type' => 'string']),
            new Constraints\Email(),
        ];
    }
}
