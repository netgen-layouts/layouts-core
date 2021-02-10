<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\ValueStatusTrait;
use Ramsey\Uuid\UuidInterface;

final class RuleCondition extends Condition
{
    use ValueStatusTrait;

    private UuidInterface $ruleId;

    /**
     * Returns the UUID of the rule to which this condition belongs to.
     */
    public function getRuleId(): UuidInterface
    {
        return $this->ruleId;
    }
}
