<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ParameterDefinitionCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinition
     */
    public function testGetParameterDefinition(): void
    {
        $definition = new ParameterDefinition();

        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => $definition],
        );

        self::assertSame($definition, $parameterDefinitions->getParameterDefinition('name'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinition
     */
    public function testGetParameterDefinitionWithNonExistingDefinition(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Parameter definition with "test" name does not exist.');

        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()],
        );

        $parameterDefinitions->getParameterDefinition('test');
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait::getParameterDefinitions
     */
    public function testGetParameterDefinitions(): void
    {
        $definition = new ParameterDefinition();

        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => $definition],
        );

        self::assertSame(
            ['name' => $definition],
            $parameterDefinitions->getParameterDefinitions(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterDefinitionCollectionTrait::hasParameterDefinition
     */
    public function testHasParameterDefinition(): void
    {
        $parameterDefinitions = new ParameterDefinitionCollection(
            ['name' => new ParameterDefinition()],
        );

        self::assertFalse($parameterDefinitions->hasParameterDefinition('test'));
        self::assertTrue($parameterDefinitions->hasParameterDefinition('name'));
    }
}
