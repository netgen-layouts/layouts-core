<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use PHPUnit\Framework\TestCase;

final class BlockItemViewTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\BlockItemViewType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockItemViewType();
        self::assertSame('ngbm_block_item_view_type', $constraint->validatedBy());
    }
}
