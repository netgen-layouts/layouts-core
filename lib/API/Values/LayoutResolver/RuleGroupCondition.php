<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Ramsey\Uuid\UuidInterface;

final class RuleGroupCondition extends Condition
{
    use ValueStatusTrait;

    public private(set) Status $status;

    /**
     * Returns the UUID of the rule group to which this condition belongs to.
     */
    public private(set) UuidInterface $ruleGroupId;
}
