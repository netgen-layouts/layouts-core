<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\TestCase;

final class ParameterCollectionTraitTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\ParameterCollectionTrait::getParameter
     */
    public function testGetParameter(): void
    {
        $parameter = new Parameter();

        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => $parameter]],
        );

        self::assertSame($parameter, $parameters->getParameter('name'));
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterCollectionTrait::getParameter
     */
    public function testGetParameterWithNonExistingParameter(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Parameter with "test" name does not exist.');

        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => new Parameter()]],
        );

        $parameters->getParameter('test');
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterCollectionTrait::getParameters
     */
    public function testGetParameters(): void
    {
        $parameter = new Parameter();

        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => $parameter]],
        );

        self::assertSame(
            ['name' => $parameter],
            $parameters->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Parameters\ParameterCollectionTrait::hasParameter
     */
    public function testHasParameter(): void
    {
        $parameters = ParameterCollection::fromArray(
            ['parameters' => ['name' => new Parameter()]],
        );

        self::assertFalse($parameters->hasParameter('test'));
        self::assertTrue($parameters->hasParameter('name'));
    }
}
