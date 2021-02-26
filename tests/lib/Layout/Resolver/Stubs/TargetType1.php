<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Stubs;

use Netgen\Layouts\Layout\Resolver\TargetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class TargetType1 extends TargetType
{
    private ?int $value;

    public function __construct(?int $value = null)
    {
        $this->value = $value;
    }

    public static function getType(): string
    {
        return 'target1';
    }

    public function getConstraints(): array
    {
        return [new Constraints\NotBlank()];
    }

    public function provideValue(Request $request): ?int
    {
        return $this->value;
    }
}
