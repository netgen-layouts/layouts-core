<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct;
use PHPUnit\Framework\TestCase;

final class ConfigAwareStructTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Structs\ConfigAwareStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ConfigAwareStruct();
        self::assertSame('nglayouts_config_aware_struct', $constraint->validatedBy());
    }
}
