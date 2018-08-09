<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Utils\HydratorTrait;

final class Target implements APITarget
{
    use HydratorTrait;
    use ValueStatusTrait;

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
     * @var mixed
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
