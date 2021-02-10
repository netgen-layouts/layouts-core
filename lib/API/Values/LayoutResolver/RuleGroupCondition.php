<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\ValueStatusTrait;
use Ramsey\Uuid\UuidInterface;

final class RuleGroupCondition extends Condition
{
    use ValueStatusTrait;

    private UuidInterface $ruleGroupId;

    /**
     * Returns the UUID of the rule group to which this condition belongs to.
     */
    public function getRuleGroupId(): UuidInterface
    {
        return $this->ruleGroupId;
    }
}
