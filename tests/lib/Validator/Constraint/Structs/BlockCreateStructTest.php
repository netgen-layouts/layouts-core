<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\BlockCreateStruct::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockCreateStruct();
        $this->assertEquals('ngbm_block_create_struct', $constraint->validatedBy());
    }
}
