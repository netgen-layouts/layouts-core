<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

final class NullBlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\NullBlockDefinition
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new NullBlockDefinition();
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('null', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Invalid block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIcon
     */
    public function testGetIcon()
    {
        $this->assertEquals('', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isTranslatable
     */
    public function testIsTranslatable()
    {
        $this->assertFalse($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getForms
     */
    public function testGetForms()
    {
        $this->assertEquals([], $this->blockDefinition->getForms());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasForm
     */
    public function testHasForm()
    {
        $this->assertFalse($this->blockDefinition->hasForm('content'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getForm
     */
    public function testGetForm()
    {
        $this->assertNull($this->blockDefinition->getForm('content'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollections
     */
    public function testGetCollections()
    {
        $this->assertEquals([], $this->blockDefinition->getCollections());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasCollection
     */
    public function testHasCollection()
    {
        $this->assertFalse($this->blockDefinition->hasCollection('collection'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollection
     */
    public function testGetCollection()
    {
        $this->assertNull($this->blockDefinition->getCollection('collection'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypes
     */
    public function testGetViewTypes()
    {
        $this->assertEquals([], $this->blockDefinition->getViewTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers()
    {
        $this->assertEquals([], $this->blockDefinition->getViewTypeIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasViewType
     */
    public function testHasViewType()
    {
        $this->assertFalse($this->blockDefinition->hasViewType('large'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewType
     */
    public function testGetViewType()
    {
        $this->assertNull($this->blockDefinition->getViewType('large'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        $this->assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isContextual
     */
    public function testIsContextual()
    {
        $this->assertFalse($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isCacheable
     */
    public function testIsCacheable()
    {
        $this->assertFalse($this->blockDefinition->isCacheable(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions()
    {
        $this->assertEquals([], $this->blockDefinition->getConfigDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPlugin()
    {
        $this->assertFalse($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin()
    {
        $this->assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }
}
