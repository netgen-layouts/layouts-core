<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

final class ParameterCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameter
     */
    public function testGetParameter(): void
    {
        $parameter = new Parameter();

        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => $parameter]]
        );

        self::assertSame($parameter, $parameters->getParameter('name'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameter
     */
    public function testGetParameterWithNonExistingParameter(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Parameter with "test" name does not exist.');

        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => new Parameter()]]
        );

        $parameters->getParameter('test');
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameters
     */
    public function testGetParameters(): void
    {
        $parameter = new Parameter();

        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => $parameter]]
        );

        self::assertSame(
            ['name' => $parameter],
            $parameters->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::hasParameter
     */
    public function testHasParameter(): void
    {
        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => new Parameter()]]
        );

        self::assertFalse($parameters->hasParameter('test'));
        self::assertTrue($parameters->hasParameter('name'));
    }
}
