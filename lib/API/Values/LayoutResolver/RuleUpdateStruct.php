<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

final class RuleUpdateStruct
{
    /**
     * The UUID of the layout to which the rule will be linked.
     *
     * Set to "false" to remove the mapping.
     *
     * @var \Ramsey\Uuid\UuidInterface|bool|null
     */
    public $layoutId;

    /**
     * Description of the rule.
     */
    public ?string $description = null;

    /**
     * Description of the rule.
     *
     * @deprecated use self::$description instead
     */
    public ?string $comment = null;
}
