<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Stubs;

use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class ConditionType1 extends ConditionType
{
    private bool $matches;

    public function __construct(bool $matches = true)
    {
        $this->matches = $matches;
    }

    public static function getType(): string
    {
        return 'condition1';
    }

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function matches(Request $request, $value): bool
    {
        return $this->matches;
    }

    /**
     * @param mixed $value
     *
     * @return string[]
     */
    public function export($value): array
    {
        return ['some_value_exported'];
    }
}
