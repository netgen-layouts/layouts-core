<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\LayoutZones;

class LayoutZonesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\LayoutZones::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new LayoutZones();
        self::assertEquals('ngbm_layout_zones', $constraint->validatedBy());
    }
}
