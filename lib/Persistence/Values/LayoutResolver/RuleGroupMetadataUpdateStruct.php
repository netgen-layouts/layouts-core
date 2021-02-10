<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroupMetadataUpdateStruct
{
    use HydratorTrait;

    /**
     * Flag indicating if the rule group will be enabled or not.
     */
    public ?bool $enabled = null;

    /**
     * Priority of the rule group.
     */
    public ?int $priority = null;
}
