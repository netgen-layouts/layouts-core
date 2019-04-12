<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\BlockViewType;
use PHPUnit\Framework\TestCase;

final class BlockViewTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\BlockViewType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockViewType();
        self::assertSame('nglayouts_block_view_type', $constraint->validatedBy());
    }
}
