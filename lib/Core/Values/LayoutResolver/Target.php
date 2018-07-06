<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;

final class Target extends Value implements APITarget
{
    /**
     * @var int|string
     */
    private $id;

    /**
     * @var int|string
     */
    private $ruleId;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    private $targetType;

    /**
     * @var int|string|float
     */
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function getRuleId()
    {
        return $this->ruleId;
    }

    public function getTargetType(): TargetTypeInterface
    {
        return $this->targetType;
    }

    public function getValue()
    {
        return $this->value;
    }
}
