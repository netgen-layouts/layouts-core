<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

final class RuleCondition extends Condition
{
    /**
     * ID of the rule where the condition is located.
     */
    public int $ruleId;

    /**
     * UUID of the rule where the condition is located.
     */
    public string $ruleUuid;
}
