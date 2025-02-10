<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Handler;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class DynamicParameter
{
    public string $parameterName;

    public function __construct(string $parameterName)
    {
        $this->parameterName = $parameterName;
    }
}
