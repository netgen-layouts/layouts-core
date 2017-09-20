<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getFormOptions
     */
    public function testGetFormOptions()
    {
        $this->assertEquals(
            array(),
            $this->mapper->getFormOptions()
        );
    }
}
