<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Symfony\Component\Uid\Uuid;

final class RuleUpdateStruct
{
    /**
     * The UUID of the layout to which the rule will be linked.
     *
     * Set to "false" to remove the mapping.
     */
    public Uuid|false|null $layoutId = null;

    /**
     * Description of the rule.
     */
    public ?string $description = null;
}
