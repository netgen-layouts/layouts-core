<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
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

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        return [
            new Constraints\Type(
                [
                    'type' => 'string',
                ]
            ),
        ];
    }
}
