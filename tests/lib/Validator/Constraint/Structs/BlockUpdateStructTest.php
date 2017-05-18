<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct;
use PHPUnit\Framework\TestCase;

class BlockUpdateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockUpdateStruct();
        $this->assertEquals('ngbm_block_update_struct', $constraint->validatedBy());
    }
}
