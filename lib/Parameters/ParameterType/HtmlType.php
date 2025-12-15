<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterType;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate HTML markup.
 *
 * It will be filtered by the system to remove any unsafe markup.
 */
final class HtmlType extends ParameterType
{
    public function __construct(
        private HtmlSanitizerInterface $sanitizer,
    ) {}

    public static function getIdentifier(): string
    {
        return 'html';
    }

    public function isValueEmpty(ParameterDefinition $parameterDefinition, mixed $value): bool
    {
        return $value === null || $value === '';
    }

    public function toHash(ParameterDefinition $parameterDefinition, mixed $value): string
    {
        return $this->sanitizer->sanitize($value ?? '');
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, mixed $value): array
    {
        return [
            new Constraints\Type(type: 'string'),
        ];
    }
}
