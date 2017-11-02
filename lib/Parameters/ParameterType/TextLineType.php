<?php

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate string.
 */
final class TextLineType extends ParameterType
{
    public function getIdentifier()
    {
        return 'text_line';
    }

    protected function getValueConstraints(ParameterInterface $parameter, $value)
    {
        return array(
            new Constraints\Type(
                array(
                    'type' => 'string',
                )
            ),
        );
    }
}
