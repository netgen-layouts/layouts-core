<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Form\TargetType;

use Netgen\Layouts\Layout\Resolver\Form\TargetType\Mapper;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\EmptyTargetTypeMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mapper::class)]
final class MapperTest extends TestCase
{
    private EmptyTargetTypeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new EmptyTargetTypeMapper();
    }

    public function testGetFormOptions(): void
    {
        self::assertSame(
            [],
            $this->mapper->getFormOptions(),
        );
    }
}
