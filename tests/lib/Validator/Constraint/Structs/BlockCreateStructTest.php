<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Validator\Constraint\Structs\BlockCreateStruct;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockCreateStruct::class)]
final class BlockCreateStructTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new BlockCreateStruct();
        self::assertSame('nglayouts_block_create_struct', $constraint->validatedBy());
    }
}
