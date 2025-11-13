<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterStruct::class)]
final class ParameterStructTest extends TestCase
{
    public function testValidatedBy(): void
    {
        $constraint = new ParameterStruct(parameterDefinitions: new BlockDefinition());
        self::assertSame('nglayouts_parameter_struct', $constraint->validatedBy());
    }
}
