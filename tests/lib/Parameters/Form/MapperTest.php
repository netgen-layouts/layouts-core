<?php

namespace Netgen\BlockManager\Tests\Parameters\Form;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterType\TextLineType;
use Netgen\BlockManager\Tests\Parameters\Stubs\ParameterDefinition;
use PHPUnit\Framework\TestCase;

final class MapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Parameters\Form\MapperInterface
     */
    private $mapper;

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
                new ParameterDefinition(
                    array(
                        'name' => 'name',
                        'type' => new TextLineType(),
                    )
                )
            )
        );
    }
}
