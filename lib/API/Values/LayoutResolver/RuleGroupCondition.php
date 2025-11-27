<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Ramsey\Uuid\UuidInterface;

final class RuleGroupCondition extends Condition
{
    /**
     * Returns the UUID of the rule group to which this condition belongs to.
     */
    public private(set) UuidInterface $ruleGroupId;
}
