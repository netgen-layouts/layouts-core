<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\ConditionType;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;

final class NullConditionType implements ConditionTypeInterface
{
    /**
     * @var string
     */
    private $conditionType;

    public function __construct(string $conditionType)
    {
        $this->conditionType = $conditionType;
    }

    public function getType(): string
    {
        return $this->conditionType;
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function matches(Request $request, $value): bool
    {
        return true;
    }
}
