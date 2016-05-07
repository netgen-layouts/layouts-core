<?php

namespace Netgen\BlockManager\Tests\Validator\Constraint;

use Netgen\BlockManager\Validator\Constraint\QueryType;

class QueryTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\QueryType::validatedBy
     */
    public function testValidatedBy()
    {
        $constraint = new QueryType();
        self::assertEquals('ngbm_query_type', $constraint->validatedBy());
    }
}
