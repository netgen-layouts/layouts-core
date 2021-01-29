<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

final class RuleGroupCondition extends Condition
{
    /**
     * ID of the rule group where the condition is located.
     *
     * @var int
     */
    public $ruleGroupId;

    /**
     * UUID of the rule group where the condition is located.
     *
     * @var string
     */
    public $ruleGroupUuid;
}
