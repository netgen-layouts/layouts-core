<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\MapperInterface;
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
