<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Utils\HydratorTrait;

final class RuleMetadataUpdateStruct
{
    use HydratorTrait;

    /**
     * Flag indicating if the rule will be enabled or not.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Priority of the rule.
     *
     * @var int
     */
    public $priority;
}
