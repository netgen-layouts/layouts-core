<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Exception\Layout\TargetException;
use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Netgen\Layouts\Layout\Resolver\ValueObjectProviderInterface;
use Netgen\Layouts\Utils\HydratorTrait;
use Symfony\Component\Uid\Uuid;

final class Target implements Value
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) Uuid $id;

    /**
     * Returns the UUID of the rule where this target belongs.
     */
    public private(set) Uuid $ruleId;

    /**
     * Returns the target type.
     */
    public private(set) TargetTypeInterface $targetType;

    /**
     * Returns the target value.
     */
    public private(set) int|string $value;

    /**
     * Returns the value object if the target type supports it.
     */
    public private(set) ?object $valueObject {
        get {
            if (!$this->targetType instanceof ValueObjectProviderInterface) {
                throw TargetException::valueObjectNotSupported($this->targetType::getType());
            }

            return $this->valueObject ??= $this->targetType->getValueObject($this->value);
        }
    }
}
