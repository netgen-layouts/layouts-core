<?php

namespace Netgen\BlockManager\Tests\Parameters\Form;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = $this->getMockForAbstractClass(Mapper::class);
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Form\Mapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(),
            $this->mapper->mapOptions(
                new Parameter('name', new TextLineType())
            )
        );
    }
}