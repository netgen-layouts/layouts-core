<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate an URL as a string.
 */
final class UrlType extends ParameterType
{
    public static function getIdentifier(): string
    {
        return 'url';
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'string']),
            new Constraints\Url(),
        ];
    }
}
