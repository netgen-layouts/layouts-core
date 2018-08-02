<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ConditionType2 implements ConditionTypeInterface
{
    /**
     * @var bool
     */
    private $matches = true;

    public function __construct(bool $matches = true)
    {
        $this->matches = $matches;
    }

    public static function getType(): string
    {
        return 'condition2';
    }

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function matches(Request $request, $value): bool
    {
        return $this->matches;
    }
}
