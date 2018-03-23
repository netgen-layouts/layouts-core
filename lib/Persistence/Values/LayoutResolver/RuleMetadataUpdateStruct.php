<?php

namespace Netgen\BlockManager\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class RuleMetadataUpdateStruct extends Value
{
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
