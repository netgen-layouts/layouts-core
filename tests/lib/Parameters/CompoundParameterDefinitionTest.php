<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\CompoundParameterDefinition;
use Netgen\Layouts\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class CompoundParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\CompoundParameterDefinition::getParameterDefinition
     * @covers \Netgen\Layouts\Parameters\CompoundParameterDefinition::getParameterDefinitions
     * @covers \Netgen\Layouts\Parameters\CompoundParameterDefinition::hasParameterDefinition
     */
    public function testSetProperties(): void
    {
        $innerDefinition = new ParameterDefinition();

        $parameterDefinition = CompoundParameterDefinition::fromArray(
            [
                'isRequired' => false,
                'parameterDefinitions' => ['name' => $innerDefinition],
            ],
        );

        self::assertSame(['name' => $innerDefinition], $parameterDefinition->getParameterDefinitions());

        self::assertFalse($parameterDefinition->hasParameterDefinition('test'));
        self::assertTrue($parameterDefinition->hasParameterDefinition('name'));

        try {
            $parameterDefinition->getParameterDefinition('test');
            self::fail('Fetched a parameter in empty collection.');
        } catch (ParameterException $e) {
            // Do nothing
        }

        self::assertSame($innerDefinition, $parameterDefinition->getParameterDefinition('name'));
    }
}
