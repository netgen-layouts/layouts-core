<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleGroupMetadataUpdateStruct
{
    use HydratorTrait;

    /**
     * Flag indicating if the rule group will be enabled or not.
     *
     * @var bool|null
     */
    public $enabled;

    /**
     * Priority of the rule group.
     *
     * @var int|null
     */
    public $priority;
}
