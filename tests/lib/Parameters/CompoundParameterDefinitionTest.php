<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\CompoundParameterDefinition;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class CompoundParameterDefinitionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::hasParameterDefinition
     */
    public function testDefaultProperties(): void
    {
        $parameterDefinition = new CompoundParameterDefinition();

        self::assertSame([], $parameterDefinition->getParameterDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinition
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::getParameterDefinitions
     * @covers \Netgen\BlockManager\Parameters\CompoundParameterDefinition::hasParameterDefinition
     */
    public function testSetProperties(): void
    {
        $innerDefinition = new ParameterDefinition();

        $parameterDefinition = CompoundParameterDefinition::fromArray(
            [
                'parameterDefinitions' => ['name' => $innerDefinition],
            ]
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
