<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterType\Html\HtmlPurifier;
use Symfony\Component\Validator\Constraints;

/**
 * Parameter type used to store and validate HTML markup.
 *
 * It will be filtered by the system to remove any unsafe markup.
 */
final class HtmlType extends ParameterType
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterType\Html\HtmlPurifier
     */
    private $htmlPurifier;

    public function __construct(HtmlPurifier $htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    public function getIdentifier(): string
    {
        return 'html';
    }

    public function toHash(ParameterDefinition $parameterDefinition, $value)
    {
        return $this->htmlPurifier->purify($value ?? '');
    }

    protected function getValueConstraints(ParameterDefinition $parameterDefinition, $value): array
    {
        return [
            new Constraints\Type(['type' => 'string']),
        ];
    }
}
