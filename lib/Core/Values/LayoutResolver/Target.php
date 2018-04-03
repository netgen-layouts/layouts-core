<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\Value;

final class Target extends Value implements APITarget
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int|string
     */
    protected $ruleId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    /**
     * @var int|string|float
     */
    protected $value;

    public function getId()
    {
        return $this->id;
    }

    public function getRuleId()
    {
        return $this->ruleId;
    }

    public function getTargetType()
    {
        return $this->targetType;
    }

    public function getValue()
    {
        return $this->value;
    }
}
