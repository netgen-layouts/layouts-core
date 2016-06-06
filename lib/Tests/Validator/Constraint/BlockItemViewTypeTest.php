<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\BlockItemViewType;

class BlockItemViewTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\BlockItemViewType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockItemViewType();
        self::assertEquals('ngbm_block_item_view_type', $constraint->validatedBy());
    }
}
