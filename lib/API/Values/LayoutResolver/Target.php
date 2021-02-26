<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Target implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    private UuidInterface $id;

    private UuidInterface $ruleId;

    private TargetTypeInterface $targetType;

    /**
     * @var int|string
     */
    private $value;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Returns the UUID of the rule where this target belongs.
     */
    public function getRuleId(): UuidInterface
    {
        return $this->ruleId;
    }

    /**
     * Returns the target type.
     */
    public function getTargetType(): TargetTypeInterface
    {
        return $this->targetType;
    }

    /**
     * Returns the target value.
     *
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }
}
