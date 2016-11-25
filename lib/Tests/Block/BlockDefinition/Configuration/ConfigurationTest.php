<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration(
            array(
                'identifier' => 'block_definition',
                'name' => 'Block definition',
                'forms' => array(
                    'content' => new Form(array('identifier' => 'content')),
                ),
                'viewTypes' => array(
                    'large' => new ViewType(array('identifier' => 'large')),
                    'small' => new ViewType(array('identifier' => 'small')),
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Block definition', $this->configuration->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals(
            array(
                'content' => new Form(array('identifier' => 'content')),
            ),
            $this->configuration->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::hasForm
     */
    public function testHasForm()
    {
        $this->assertTrue($this->configuration->hasForm('content'));
        $this->assertFalse($this->configuration->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForm
     */
    public function testGetForm()
    {
        $this->assertEquals(
            new Form(array('identifier' => 'content')),
            $this->configuration->getForm('content')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForm
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetFormThrowsInvalidArgumentException()
    {
        $this->configuration->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewTypes
     */
    public function testGetViewTypes()
    {
        $this->assertEquals(
            array(
                'large' => new ViewType(array('identifier' => 'large')),
                'small' => new ViewType(array('identifier' => 'small')),
            ),
            $this->configuration->getViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers()
    {
        $this->assertEquals(
            array('large', 'small'),
            $this->configuration->getViewTypeIdentifiers()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::hasViewType
     */
    public function testHasViewType()
    {
        $this->assertTrue($this->configuration->hasViewType('large'));
        $this->assertFalse($this->configuration->hasViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewType
     */
    public function testGetViewType()
    {
        $this->assertEquals(
            new ViewType(array('identifier' => 'large')),
            $this->configuration->getViewType('large')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewType
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetViewTypeThrowsInvalidArgumentException()
    {
        $this->configuration->getViewType('unknown');
    }
}
