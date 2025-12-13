<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Symfony\Component\Uid\Uuid;

final class RuleCondition extends Condition
{
    /**
     * Returns the UUID of the rule to which this condition belongs to.
     */
    public private(set) Uuid $ruleId;
}
