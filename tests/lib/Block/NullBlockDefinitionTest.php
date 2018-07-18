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
        $this->assertSame('definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Invalid block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        $this->assertSame('', $this->blockDefinition->getIcon());
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
        $this->assertSame([], $this->blockDefinition->getForms());
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
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Form "content" does not exist in "definition" block definition.
     */
    public function testGetForm(): void
    {
        $this->blockDefinition->getForm('content');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        $this->assertSame([], $this->blockDefinition->getCollections());
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
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Collection "collection" does not exist in "definition" block definition.
     */
    public function testGetCollection(): void
    {
        $this->blockDefinition->getCollection('collection');
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        $this->assertSame([], $this->blockDefinition->getViewTypes());
    }

    /**
     * @covers \Netgen\BlockManager\Block\NullBlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers(): void
    {
        $this->assertSame([], $this->blockDefinition->getViewTypeIdentifiers());
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
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage View type "large" does not exist in "definition" block definition.
     */
    public function testGetViewType(): void
    {
        $this->blockDefinition->getViewType('large');
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
