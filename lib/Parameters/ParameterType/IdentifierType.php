<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an identifier.
 *
 * An identifier is a string starting with a letter, followed by any
 * combination of letters, numbers and underscores.
 */
class IdentifierType extends ParameterType
{
    public function getIdentifier()
    {
        return 'identifier';
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(array('type' => 'string')),
            new Constraints\Regex(
                array(
                    'pattern' => '/^[A-Za-z]([A-Za-z0-9_])*$/',
                )
            ),
        );
    }
}
