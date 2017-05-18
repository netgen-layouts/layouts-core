<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use PHPUnit\Framework\TestCase;

class ParameterStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ParameterStruct();
        $this->assertEquals('ngbm_parameter_struct', $constraint->validatedBy());
    }
}
