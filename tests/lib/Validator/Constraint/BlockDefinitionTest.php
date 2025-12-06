<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint;

use Netgen\Layouts\Validator\Constraint\BlockDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockDefinition::class)]
final class BlockDefinitionTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new BlockDefinition();
        self::assertSame('nglayouts_block_definition', $constraint->validatedBy());
    }
}
