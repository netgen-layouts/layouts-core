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
            'block_definition',
            array(
                'content' => new Form('content', 'form_type', true),
            ),
            array(
                'large' => new ViewType('large', 'Large'),
                'small' => new ViewType('small', 'Small'),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals(
            array(
                'content' => new Form('content', 'form_type', true),
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
            new Form('content', 'form_type', true),
            $this->configuration->getForm('content')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getForm
     * @expectedException \RuntimeException
     */
    public function testGetFormThrowsRuntimeException()
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
                'large' => new ViewType('large', 'Large'),
                'small' => new ViewType('small', 'Small'),
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
            new ViewType('large', 'Large'),
            $this->configuration->getViewType('large')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getViewType
     * @expectedException \RuntimeException
     */
    public function testGetViewTypeThrowsRuntimeException()
    {
        $this->configuration->getViewType('unknown');
    }
}
