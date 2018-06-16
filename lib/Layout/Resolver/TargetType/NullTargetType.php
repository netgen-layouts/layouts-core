<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;

final class NullTargetType implements TargetTypeInterface
{
    /**
     * @var string
     */
    private $targetType;

    public function __construct(string $targetType)
    {
        $this->targetType = $targetType;
    }

    public function getType(): string
    {
        return $this->targetType;
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function provideValue(Request $request)
    {
        return null;
    }
}
