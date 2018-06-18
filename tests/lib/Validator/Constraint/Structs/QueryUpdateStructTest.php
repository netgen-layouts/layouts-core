<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Validator\Constraint\Structs;

use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct;
use PHPUnit\Framework\TestCase;

final class QueryUpdateStructTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new QueryUpdateStruct();
        $this->assertSame('ngbm_query_update_struct', $constraint->validatedBy());
    }
}
