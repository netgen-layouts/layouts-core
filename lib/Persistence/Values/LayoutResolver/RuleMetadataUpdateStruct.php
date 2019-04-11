<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Utils\HydratorTrait;

final class RuleMetadataUpdateStruct
{
    use HydratorTrait;

    /**
     * Flag indicating if the rule will be enabled or not.
     *
     * @var bool|null
     */
    public $enabled;

    /**
     * Priority of the rule.
     *
     * @var int|null
     */
    public $priority;
}
