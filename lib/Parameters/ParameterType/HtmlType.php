<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate HTML markup.
 *
 * It will be filtered by the system to remove any unsafe markup.
 */
final class HtmlType extends ParameterType
{
    public function getIdentifier()
    {
        return 'html';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value)
    {
        return [
            new Constraints\Type(['type' => 'string']),
        ];
    }
}
