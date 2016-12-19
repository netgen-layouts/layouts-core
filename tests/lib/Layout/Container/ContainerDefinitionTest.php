<?php

namespace Netgen\BlockManager\Tests\Layout\Container;

use Netgen\BlockManager\Layout\Container\ContainerDefinition;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\PlaceholderDefinition;
use PHPUnit\Framework\TestCase;

class ContainerDefinitionTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition
     */
    protected $containerDefinition;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);

        $this->containerDefinition = new ContainerDefinition(
            array(
                'identifier' => 'container_definition',
                'placeholders' => array(
                    'left' => new PlaceholderDefinition(array('identifier' => 'left')),
                    'right' => new PlaceholderDefinition(array('identifier' => 'right')),
                ),
                'config' => $this->configMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('container_definition', $this->containerDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals(
            array(
                'left' => new PlaceholderDefinition(array('identifier' => 'left')),
                'right' => new PlaceholderDefinition(array('identifier' => 'right')),
            ),
            $this->containerDefinition->getPlaceholders()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition::getPlaceholder
     */
    public function testGetPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'left')),
            $this->containerDefinition->getPlaceholder('left')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition::getPlaceholder
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetPlaceholderThrowsInvalidArgumentException()
    {
        $this->containerDefinition->getPlaceholder('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition::hasPlaceholder
     */
    public function testHasPlaceholder()
    {
        $this->assertTrue($this->containerDefinition->hasPlaceholder('left'));
        $this->assertFalse($this->containerDefinition->hasPlaceholder('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->containerDefinition->getConfig());
    }
}
