<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Validator\Constraint\Structs\BlockCreateStruct;
use PHPUnit\Framework\TestCase;

final class BlockCreateStructTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Structs\BlockCreateStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new BlockCreateStruct();
        self::assertSame('nglayouts_block_create_struct', $constraint->validatedBy());
    }
}
