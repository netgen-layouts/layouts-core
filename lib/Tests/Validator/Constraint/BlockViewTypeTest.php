<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\BlockViewType;

class BlockViewTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\BlockViewType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockViewType();
        self::assertEquals('ngbm_block_view_type', $constraint->validatedBy());
    }
}
