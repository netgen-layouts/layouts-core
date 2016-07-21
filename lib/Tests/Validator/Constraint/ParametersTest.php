<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Parameters;
use PHPUnit\Framework\TestCase;

class ParametersTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Parameters::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new Parameters();
        $this->assertEquals('ngbm_parameters', $constraint->validatedBy());
    }
}
