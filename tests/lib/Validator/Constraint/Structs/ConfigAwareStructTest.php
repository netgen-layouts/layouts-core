<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct;
use PHPUnit\Framework\TestCase;

class ConfigAwareStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\ConfigAwareStruct::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new ConfigAwareStruct();
        $this->assertEquals('ngbm_config_aware_struct', $constraint->validatedBy());
    }
}
