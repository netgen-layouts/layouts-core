<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

final class RuleUpdateStruct
{
    /**
     * The ID of the layout to which the rule will be linked.
     *
     * Set to 0 to remove the mapping.
     *
     * @var int|string|null
     */
    public $layoutId;

    /**
     * Description of the rule.
     *
     * @var string|null
     */
    public $comment;
}
