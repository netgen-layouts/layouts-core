<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\EmptyConditionTypeMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mapper::class)]
final class MapperTest extends TestCase
{
    private EmptyConditionTypeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new EmptyConditionTypeMapper();
    }

    public function testGetFormOptions(): void
    {
        self::assertSame(
            [],
            $this->mapper->getFormOptions(),
        );
    }
}
