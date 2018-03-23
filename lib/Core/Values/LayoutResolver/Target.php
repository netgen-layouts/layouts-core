<?php

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Value;

final class Target extends Value implements APITarget
{
    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var int|string
     */
    protected $ruleId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    /**
     * @var bool
     */
    protected $published;

    /**
     * @var int|string|float
     */
    protected $value;

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getRuleId()
    {
        return $this->ruleId;
    }

    public function getTargetType()
    {
        return $this->targetType;
    }

    public function isPublished()
    {
        return $this->published;
    }

    public function getValue()
    {
        return $this->value;
    }
}
