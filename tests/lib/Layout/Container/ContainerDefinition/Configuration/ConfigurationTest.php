<?php

namespace Netgen\BlockManager\Tests\Layout\Container\ContainerDefinition\Configuration;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Form;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration
     */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration(
            array(
                'identifier' => 'container_definition',
                'name' => 'Container definition',
                'forms' => array(
                    'full' => new Form(array('identifier' => 'full', 'type' => 'container')),
                ),
                'placeholderForms' => array(
                    'full' => new Form(array('identifier' => 'full', 'type' => 'placeholder')),
                ),
                'viewTypes' => array(
                    'view_1' => new ViewType(array('identifier' => 'view_1')),
                    'view_2' => new ViewType(array('identifier' => 'view_2')),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::__construct
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Container definition', $this->configuration->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals(
            array(
                'full' => new Form(array('identifier' => 'full', 'type' => 'container')),
            ),
            $this->configuration->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::hasForm
     */
    public function testHasForm()
    {
        $this->assertTrue($this->configuration->hasForm('full'));
        $this->assertFalse($this->configuration->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getForm
     */
    public function testGetForm()
    {
        $this->assertEquals(
            new Form(array('identifier' => 'full', 'type' => 'container')),
            $this->configuration->getForm('full')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getForm
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetFormThrowsInvalidArgumentException()
    {
        $this->configuration->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getPlaceholderForms
     */
    public function testGetPlaceholderForms()
    {
        $this->assertEquals(
            array(
                'full' => new Form(array('identifier' => 'full', 'type' => 'placeholder')),
            ),
            $this->configuration->getPlaceholderForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::hasPlaceholderForm
     */
    public function testHasPlaceholderForm()
    {
        $this->assertTrue($this->configuration->hasPlaceholderForm('full'));
        $this->assertFalse($this->configuration->hasPlaceholderForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getPlaceholderForm
     */
    public function testGetPlaceholderForm()
    {
        $this->assertEquals(
            new Form(array('identifier' => 'full', 'type' => 'placeholder')),
            $this->configuration->getPlaceholderForm('full')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getPlaceholderForm
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetPlaceholderFormThrowsInvalidArgumentException()
    {
        $this->configuration->getPlaceholderForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getViewTypes
     */
    public function testGetViewTypes()
    {
        $this->assertEquals(
            array(
                'view_1' => new ViewType(array('identifier' => 'view_1')),
                'view_2' => new ViewType(array('identifier' => 'view_2')),
            ),
            $this->configuration->getViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers()
    {
        $this->assertEquals(
            array('view_1', 'view_2'),
            $this->configuration->getViewTypeIdentifiers()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::hasViewType
     */
    public function testHasViewType()
    {
        $this->assertTrue($this->configuration->hasViewType('view_1'));
        $this->assertFalse($this->configuration->hasViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getViewType
     */
    public function testGetViewType()
    {
        $this->assertEquals(
            new ViewType(array('identifier' => 'view_1')),
            $this->configuration->getViewType('view_1')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration::getViewType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetViewTypeThrowsInvalidArgumentException()
    {
        $this->configuration->getViewType('unknown');
    }
}
