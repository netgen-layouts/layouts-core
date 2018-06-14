<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->blockDefinition = new NullBlockDefinition('definition');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::__construct
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('Invalid block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        $this->assertEquals('', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isTranslatable
     */
    public function testIsTranslatable(): void
    {
        $this->assertFalse($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getForms
     */
    public function testGetForms(): void
    {
        $this->assertEquals([], $this->blockDefinition->getForms());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasForm
     */
    public function testHasForm(): void
    {
        $this->assertFalse($this->blockDefinition->hasForm('content'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getForm
     */
    public function testGetForm(): void
    {
        $this->assertNull($this->blockDefinition->getForm('content'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        $this->assertEquals([], $this->blockDefinition->getCollections());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasCollection
     */
    public function testHasCollection(): void
    {
        $this->assertFalse($this->blockDefinition->hasCollection('collection'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollection
     */
    public function testGetCollection(): void
    {
        $this->assertNull($this->blockDefinition->getCollection('collection'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        $this->assertEquals([], $this->blockDefinition->getViewTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers(): void
    {
        $this->assertEquals([], $this->blockDefinition->getViewTypeIdentifiers());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasViewType
     */
    public function testHasViewType(): void
    {
        $this->assertFalse($this->blockDefinition->hasViewType('large'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewType
     */
    public function testGetViewType(): void
    {
        $this->assertNull($this->blockDefinition->getViewType('large'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        $this->assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        $this->assertFalse($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::isCacheable
     */
    public function testIsCacheable(): void
    {
        $this->assertFalse($this->blockDefinition->isCacheable(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions(): void
    {
        $this->assertEquals([], $this->blockDefinition->getConfigDefinitions());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPlugin(): void
    {
        $this->assertFalse($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin(): void
    {
        $this->assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }
}
