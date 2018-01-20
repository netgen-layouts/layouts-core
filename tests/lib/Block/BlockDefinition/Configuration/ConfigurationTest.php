<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration(
            array(
                'identifier' => 'block_definition',
                'name' => 'Block definition',
                'icon' => '/icon.svg',
                'isTranslatable' => true,
                'forms' => array(
                    'content' => new Form(array('identifier' => 'content')),
                ),
                'collections' => array(
                    'collection' => new Collection(array('identifier' => 'collection')),
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
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getIcon
     */
    public function testGetIcon()
    {
        $this->assertEquals('/icon.svg', $this->configuration->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::isTranslatable
     */
    public function testIsTranslatable()
    {
        $this->assertTrue($this->configuration->isTranslatable());
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
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Form "unknown" does not exist in "block_definition" block definition.
     */
    public function testGetFormThrowsBlockDefinitionException()
    {
        $this->configuration->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getCollections
     */
    public function testGetCollections()
    {
        $this->assertEquals(
            array(
                'collection' => new Collection(array('identifier' => 'collection')),
            ),
            $this->configuration->getCollections()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::hasCollection
     */
    public function testHasCollection()
    {
        $this->assertTrue($this->configuration->hasCollection('collection'));
        $this->assertFalse($this->configuration->hasCollection('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getCollection
     */
    public function testGetCollection()
    {
        $this->assertEquals(
            new Collection(array('identifier' => 'collection')),
            $this->configuration->getCollection('collection')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration::getCollection
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Collection "unknown" does not exist in "block_definition" block definition.
     */
    public function testGetCollectionThrowsBlockDefinitionException()
    {
        $this->configuration->getCollection('unknown');
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
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage View type "unknown" does not exist in "block_definition" block definition.
     */
    public function testGetViewTypeThrowsBlockDefinitionException()
    {
        $this->configuration->getViewType('unknown');
    }
}
