<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Tests\Block\Stubs\EmptyHandlerPlugin;
use Netgen\Layouts\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(NullBlockDefinition::class)]
final class NullBlockDefinitionTest extends TestCase
{
    private NullBlockDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->blockDefinition = new NullBlockDefinition('definition');
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('definition', $this->blockDefinition->identifier);
    }

    public function testGetName(): void
    {
        self::assertSame('Invalid block definition', $this->blockDefinition->name);
    }

    public function testGetIcon(): void
    {
        self::assertSame('', $this->blockDefinition->icon);
    }

    public function testGetForms(): void
    {
        self::assertSame([], $this->blockDefinition->forms);
    }

    public function testHasForm(): void
    {
        self::assertFalse($this->blockDefinition->hasForm('content'));
    }

    public function testGetForm(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Form "content" does not exist in "definition" block definition.');

        $this->blockDefinition->getForm('content');
    }

    public function testGetCollections(): void
    {
        self::assertSame([], $this->blockDefinition->collections);
    }

    public function testHasCollection(): void
    {
        self::assertFalse($this->blockDefinition->hasCollection('collection'));
    }

    public function testGetCollection(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Collection "collection" does not exist in "definition" block definition.');

        $this->blockDefinition->getCollection('collection');
    }

    public function testGetViewTypes(): void
    {
        self::assertSame([], $this->blockDefinition->viewTypes);
    }

    public function testGetViewTypeIdentifiers(): void
    {
        self::assertSame([], $this->blockDefinition->viewTypeIdentifiers);
    }

    public function testHasViewType(): void
    {
        self::assertFalse($this->blockDefinition->hasViewType('large'));
    }

    public function testGetViewType(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('View type "large" does not exist in "definition" block definition.');

        $this->blockDefinition->getViewType('large');
    }

    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(0, $dynamicParameters);
    }

    public function testIsContextual(): void
    {
        self::assertFalse($this->blockDefinition->isContextual(new Block()));
    }

    public function testHasHandlerPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasHandlerPlugin(HandlerPlugin::class));
    }

    public function testHasHandlerPluginWithUnknownPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasHandlerPlugin(EmptyHandlerPlugin::class));
    }

    public function testGetHandlerPlugin(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage(sprintf('Block definition with "%s" identifier does not have a plugin with "%s" class.', 'definition', HandlerPlugin::class));

        $this->blockDefinition->getHandlerPlugin(HandlerPlugin::class);
    }

    public function testGetHandlerPlugins(): void
    {
        self::assertCount(0, $this->blockDefinition->handlerPlugins);
    }
}
