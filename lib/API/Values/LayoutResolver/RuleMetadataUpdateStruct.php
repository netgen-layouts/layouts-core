<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Netgen\BlockManager\Value;

final class RuleMetadataUpdateStruct extends Value
{
    /**
     * New priority of the layout.
     *
     * @var int|null
     */
    public $priority;
}
