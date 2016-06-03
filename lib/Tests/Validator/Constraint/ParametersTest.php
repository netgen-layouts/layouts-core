<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Parameters;

class ParametersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Parameters::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Parameters();
        self::assertEquals('ngbm_parameters', $constraint->validatedBy());
    }
}
