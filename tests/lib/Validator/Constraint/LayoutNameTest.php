<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\LayoutName;
use PHPUnit\Framework\TestCase;

final class LayoutNameTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\LayoutName::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new LayoutName();
        self::assertSame('nglayouts_layout_name', $constraint->validatedBy());
    }
}
