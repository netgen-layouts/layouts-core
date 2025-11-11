<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

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
        self::assertSame('definition', $this->blockDefinition->getIdentifier());
    }

    public function testGetName(): void
    {
        self::assertSame('Invalid block definition', $this->blockDefinition->getName());
    }

    public function testGetIcon(): void
    {
        self::assertSame('', $this->blockDefinition->getIcon());
    }

    public function testIsTranslatable(): void
    {
        self::assertFalse($this->blockDefinition->isTranslatable());
    }

    public function testGetForms(): void
    {
        self::assertSame([], $this->blockDefinition->getForms());
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
        self::assertSame([], $this->blockDefinition->getCollections());
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
        self::assertSame([], $this->blockDefinition->getViewTypes());
    }

    public function testGetViewTypeIdentifiers(): void
    {
        self::assertSame([], $this->blockDefinition->getViewTypeIdentifiers());
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

    public function testHasPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    public function testHasPluginWithUnknownPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }

    public function testGetPlugin(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage(sprintf('Block definition with "%s" identifier does not have a plugin with "%s" class.', 'definition', HandlerPlugin::class));

        $this->blockDefinition->getPlugin(HandlerPlugin::class);
    }

    public function testGetPlugins(): void
    {
        self::assertCount(0, $this->blockDefinition->getPlugins());
    }
}
