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

    /**
     * @param string $targetType
     */
    public function __construct($targetType)
    {
        $this->targetType = $targetType;
    }

    public function getType()
    {
        return $this->targetType;
    }

    public function getConstraints()
    {
        return [];
    }

    public function provideValue(Request $request)
    {
    }
}
