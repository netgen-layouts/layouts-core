<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Stubs;

use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ConditionType1 extends ConditionType
{
    public function __construct(
        private bool $matches = true,
    ) {}

    public static function getType(): string
    {
        return 'condition1';
    }

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function matches(Request $request, int|string|array $value): bool
    {
        return $this->matches;
    }

    /**
     * @return string[]
     */
    public function export(int|string|array $value): array
    {
        return ['some_value_exported'];
    }
}
