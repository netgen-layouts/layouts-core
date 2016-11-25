<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(),
            $this->mapper->mapOptions(
                $this->createMock(ConditionTypeInterface::class)
            )
        );
    }
}
