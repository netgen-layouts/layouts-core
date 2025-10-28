<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Ramsey\Uuid\UuidInterface;

final class RuleUpdateStruct
{
    /**
     * The UUID of the layout to which the rule will be linked.
     *
     * Set to "false" to remove the mapping.
     */
    public UuidInterface|false|null $layoutId = null;

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
