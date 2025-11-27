<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterCollectionTrait;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Tests\Parameters\Stubs\ParameterCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ParameterCollectionTrait::class)]
final class ParameterCollectionTraitTest extends TestCase
{
    public function testGetParameter(): void
    {
        $parameter = new Parameter();

        $parameters = ParameterCollection::fromArray(
            ['parameters' => new ParameterList(['name' => $parameter])],
        );

        self::assertSame($parameter, $parameters->getParameter('name'));
    }

    public function testGetParameterWithNonExistingParameter(): void
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Parameter with "test" name does not exist.');

        $parameters = ParameterCollection::fromArray(
            ['parameters' => new ParameterList(['name' => new Parameter()])],
        );

        $parameters->getParameter('test');
    }

    public function testGetParameters(): void
    {
        $parameter = new Parameter();

        $parameters = ParameterCollection::fromArray(
            ['parameters' => new ParameterList(['name' => $parameter])],
        );

        self::assertSame(
            ['name' => $parameter],
            $parameters->parameters->toArray(),
        );
    }

    public function testHasParameter(): void
    {
        $parameters = ParameterCollection::fromArray(
            ['parameters' => new ParameterList(['name' => new Parameter()])],
        );

        self::assertFalse($parameters->hasParameter('test'));
        self::assertTrue($parameters->hasParameter('name'));
    }
}
