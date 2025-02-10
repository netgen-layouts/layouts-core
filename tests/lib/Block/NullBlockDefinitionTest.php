<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

use function sprintf;

final class NullBlockDefinitionTest extends TestCase
{
    private NullBlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = new NullBlockDefinition('definition');
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::__construct
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Invalid block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::isTranslatable
     */
    public function testIsTranslatable(): void
    {
        self::assertFalse($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getForms
     */
    public function testGetForms(): void
    {
        self::assertSame([], $this->blockDefinition->getForms());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::hasForm
     */
    public function testHasForm(): void
    {
        self::assertFalse($this->blockDefinition->hasForm('content'));
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getForm
     */
    public function testGetForm(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Form "content" does not exist in "definition" block definition.');

        $this->blockDefinition->getForm('content');
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        self::assertSame([], $this->blockDefinition->getCollections());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::hasCollection
     */
    public function testHasCollection(): void
    {
        self::assertFalse($this->blockDefinition->hasCollection('collection'));
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getCollection
     */
    public function testGetCollection(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Collection "collection" does not exist in "definition" block definition.');

        $this->blockDefinition->getCollection('collection');
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        self::assertSame([], $this->blockDefinition->getViewTypes());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers(): void
    {
        self::assertSame([], $this->blockDefinition->getViewTypeIdentifiers());
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::hasViewType
     */
    public function testHasViewType(): void
    {
        self::assertFalse($this->blockDefinition->hasViewType('large'));
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getViewType
     */
    public function testGetViewType(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('View type "large" does not exist in "definition" block definition.');

        $this->blockDefinition->getViewType('large');
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(0, $dynamicParameters);
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getPlugin
     */
    public function testGetPlugin(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage(sprintf('Block definition with "%s" identifier does not have a plugin with "%s" class.', 'definition', HandlerPlugin::class));

        $this->blockDefinition->getPlugin(HandlerPlugin::class);
    }

    /**
     * @covers \Netgen\Layouts\Block\NullBlockDefinition::getPlugins
     */
    public function testGetPlugins(): void
    {
        self::assertCount(0, $this->blockDefinition->getPlugins());
    }
}
