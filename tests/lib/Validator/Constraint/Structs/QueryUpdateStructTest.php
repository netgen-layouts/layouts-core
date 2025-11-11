<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Validator\Constraint\Structs\QueryUpdateStruct;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueryUpdateStruct::class)]
final class QueryUpdateStructTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new QueryUpdateStruct();
        self::assertSame('nglayouts_query_update_struct', $constraint->validatedBy());
    }
}
