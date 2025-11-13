<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockItemViewType::class)]
final class BlockItemViewTypeTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new BlockItemViewType(viewType: 'view_type', definition: new BlockDefinition());
        self::assertSame('nglayouts_block_item_view_type', $constraint->validatedBy());
    }
}
