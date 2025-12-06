<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\Layout;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Layout::class)]
final class LayoutTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new Layout();
        self::assertSame('nglayouts_layout', $constraint->validatedBy());
    }
}
