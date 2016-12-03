<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct;
use PHPUnit\Framework\TestCase;

class QueryUpdateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new QueryUpdateStruct();
        $this->assertEquals('ngbm_query_update_struct', $constraint->validatedBy());
    }
}
