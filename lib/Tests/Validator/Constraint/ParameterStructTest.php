<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use PHPUnit\Framework\TestCase;

class ParametersTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\ParameterStruct::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ParameterStruct();
        $this->assertEquals('ngbm_parameter_struct', $constraint->validatedBy());
    }
}
