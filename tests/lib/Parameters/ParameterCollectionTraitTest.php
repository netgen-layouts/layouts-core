<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters;

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

        $this->assertSame($parameter, $parameters->getParameter('name'));
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\ParameterCollectionTrait::getParameter
     * @expectedException \Netgen\BlockManager\Exception\Parameters\ParameterException
     * @expectedExceptionMessage Parameter with "test" name does not exist.
     */
    public function testGetParameterWithNonExistingParameter(): void
    {
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

        $this->assertSame(
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

        $this->assertFalse($parameters->hasParameter('test'));
        $this->assertTrue($parameters->hasParameter('name'));
    }
}
