<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Layout;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Layout::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Layout();
        self::assertEquals('ngbm_layout', $constraint->validatedBy());
    }
}
