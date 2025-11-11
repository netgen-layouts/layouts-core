<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\LayoutName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutName::class)]
final class LayoutNameTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new LayoutName();
        self::assertSame('nglayouts_layout_name', $constraint->validatedBy());
    }
}
