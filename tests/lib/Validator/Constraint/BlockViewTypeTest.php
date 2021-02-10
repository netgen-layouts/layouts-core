<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Validator\Constraint\BlockViewType;
use PHPUnit\Framework\TestCase;

final class BlockViewTypeTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\BlockViewType::getRequiredOptions
     */
    public function testGetRequiredOptions(): void
    {
        $constraint = new BlockViewType(['definition' => new BlockDefinition()]);
        self::assertSame(['definition'], $constraint->getRequiredOptions());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Constraint\BlockViewType::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockViewType(['definition' => new BlockDefinition()]);
        self::assertSame('nglayouts_block_view_type', $constraint->validatedBy());
    }
}
