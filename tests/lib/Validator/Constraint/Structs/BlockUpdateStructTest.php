<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Validator\Constraint\Structs\BlockUpdateStruct;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlockUpdateStruct::class)]
final class BlockUpdateStructTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new BlockUpdateStruct();
        self::assertSame('nglayouts_block_update_struct', $constraint->validatedBy());
    }
}
