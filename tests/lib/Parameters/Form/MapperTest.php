<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Form;

use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\Stubs\EmptyFormMapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mapper::class)]
final class MapperTest extends TestCase
{
    private EmptyFormMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new EmptyFormMapper();
    }

    public function testMapOptions(): void
    {
        self::assertSame([], $this->mapper->mapOptions(new ParameterDefinition()));
    }
}
