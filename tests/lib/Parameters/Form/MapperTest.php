<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\Form;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $this->assertSame([], $this->mapper->mapOptions(new ParameterDefinition()));
    }
}
