<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

final class RuleCreateStruct
{
    /**
     * The UUID of the layout to which the rule will be mapped.
     *
     * @var \Ramsey\Uuid\UuidInterface|null
     */
    public $layoutId;

    /**
     * Priority of the rule.
     *
     * @var int|null
     */
    public $priority;

    /**
     * Specifies if the rule will be enabled or not.
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * Description of the rule.
     *
     * @var string|null
     */
    public $comment;
}
