<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator\Constraint\Structs;

use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct;
use PHPUnit\Framework\TestCase;

final class ParameterStructTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct::getRequiredOptions
     */
    public function testGetRequiredOptions(): void
    {
        $constraint = new ParameterStruct(['parameterDefinitions' => new BlockDefinition()]);
        self::assertSame(['parameterDefinitions'], $constraint->getRequiredOptions());
    }

    /**
     * @covers \Netgen\Layouts\Validator\Constraint\Structs\ParameterStruct::validatedBy
     */
    public function testValidatedBy(): void
    {
        $constraint = new ParameterStruct(['parameterDefinitions' => new BlockDefinition()]);
        self::assertSame('nglayouts_parameter_struct', $constraint->validatedBy());
    }
}
