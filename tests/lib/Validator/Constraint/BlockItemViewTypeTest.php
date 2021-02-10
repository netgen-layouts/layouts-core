<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Validator\Constraint\BlockItemViewType;
use PHPUnit\Framework\TestCase;

final class BlockItemViewTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\BlockItemViewType::getRequiredOptions
     */
    public function testGetRequiredOptions(): void
    {
        $constraint = new BlockItemViewType(['viewType' => 'view_type', 'definition' => new BlockDefinition()]);
        self::assertSame(['viewType', 'definition'], $constraint->getRequiredOptions());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Constraint\BlockItemViewType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockItemViewType(['viewType' => 'view_type', 'definition' => new BlockDefinition()]);
        self::assertSame('nglayouts_block_item_view_type', $constraint->validatedBy());
    }
}
