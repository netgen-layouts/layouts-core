<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form;

use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\Form\MapperInterface;
use Netgen\Layouts\Parameters\ParameterDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mapper::class)]
final class MapperTest extends TestCase
{
    private MockObject&MapperInterface $mapper;

    protected function setUp(): void
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    public function testMapOptions(): void
    {
        self::assertSame([], $this->mapper->mapOptions(new ParameterDefinition()));
    }
}
