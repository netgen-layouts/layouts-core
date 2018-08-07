<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct;
use PHPUnit\Framework\TestCase;

final class BlockUpdateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockUpdateStruct();
        self::assertSame('ngbm_block_update_struct', $constraint->validatedBy());
    }
}
