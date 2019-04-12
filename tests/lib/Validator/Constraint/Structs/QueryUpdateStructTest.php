<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Validator\Constraint\Structs\QueryUpdateStruct;
use PHPUnit\Framework\TestCase;

final class QueryUpdateStructTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Structs\QueryUpdateStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new QueryUpdateStruct();
        self::assertSame('nglayouts_query_update_struct', $constraint->validatedBy());
    }
}
