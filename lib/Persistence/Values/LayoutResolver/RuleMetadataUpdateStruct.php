<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleMetadataUpdateStruct
{
    use HydratorTrait;

    /**
     * Flag indicating if the rule will be enabled or not.
     */
    public ?bool $enabled = null;

    /**
     * Priority of the rule.
     */
    public ?int $priority = null;
}
