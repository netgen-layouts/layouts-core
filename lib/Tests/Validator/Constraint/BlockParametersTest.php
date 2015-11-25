<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\BlockParameters;

class BlockParametersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\BlockParameters::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new BlockParameters();
        self::assertEquals('ngbm_block_parameters', $constraint->validatedBy());
    }
}
