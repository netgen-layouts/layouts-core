<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\ParameterStruct;
use PHPUnit\Framework\TestCase;

final class ParameterStructTest extends TestCase
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
