<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper;
use Netgen\Layouts\Layout\Resolver\Form\TargetType\MapperInterface;
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

    public function testGetFormOptions(): void
    {
        self::assertSame(
            [],
            $this->mapper->getFormOptions(),
        );
    }
}
