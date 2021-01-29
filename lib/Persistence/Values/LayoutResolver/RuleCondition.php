<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\LayoutResolver;

final class RuleCondition extends Condition
{
    /**
     * ID of the rule where the condition is located.
     *
     * @var int
     */
    public $ruleId;

    /**
     * UUID of the rule where the condition is located.
     *
     * @var string
     */
    public $ruleUuid;
}
