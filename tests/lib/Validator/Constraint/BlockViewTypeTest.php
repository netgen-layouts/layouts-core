<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockViewType::class)]
final class BlockViewTypeTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new BlockViewType(definition: new BlockDefinition());
        self::assertSame('nglayouts_block_view_type', $constraint->validatedBy());
    }
}
