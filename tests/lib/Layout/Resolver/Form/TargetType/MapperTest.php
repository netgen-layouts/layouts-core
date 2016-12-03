<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(),
            $this->mapper->mapOptions(
                $this->createMock(TargetTypeInterface::class)
            )
        );
    }
}
