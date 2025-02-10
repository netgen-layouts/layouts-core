<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Collection;
use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Exception\Block\BlockDefinitionException;
use Netgen\Layouts\Exception\Config\ConfigDefinitionException;
use Netgen\Layouts\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\Layouts\Tests\Block\Stubs\HandlerPlugin;
use Netgen\Layouts\Tests\Core\Stubs\ConfigProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

use function sprintf;

final class BlockDefinitionTest extends TestCase
{
    private BlockDefinitionHandler $handler;

    private BlockDefinition $blockDefinition;

    private Form $form;

    private Collection $collection;

    private ViewType $viewType1;

    private ViewType $viewType2;

    private ConfigDefinition $configDefinition;

    protected function setUp(): void
    {
        $this->handler = new BlockDefinitionHandler([], true);

        $this->form = Form::fromArray(['identifier' => 'content']);
        $this->collection = Collection::fromArray(['identifier' => 'collection']);
        $this->configDefinition = new ConfigDefinition();

        $this->viewType1 = ViewType::fromArray(['identifier' => 'large']);
        $this->viewType2 = ViewType::fromArray(['identifier' => 'small']);

        $this->blockDefinition = BlockDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'handlerPlugins' => [HandlerPlugin::instance([BlockDefinitionHandlerInterface::class])],
                'name' => 'Block definition',
                'icon' => '/icon.svg',
                'isTranslatable' => true,
                'forms' => [
                    'content' => $this->form,
                ],
                'collections' => [
                    'collection' => $this->collection,
                ],
                'configProvider' => ConfigProvider::fromFullConfig(
                    [
                        'large' => $this->viewType1,
                        'small' => $this->viewType2,
                    ],
                ),
                'configDefinitions' => ['config' => $this->configDefinition],
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('block_definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::isTranslatable
     */
    public function testIsTranslatable(): void
    {
        self::assertTrue($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getForms
     */
    public function testGetForms(): void
    {
        self::assertSame(
            [
                'content' => $this->form,
            ],
            $this->blockDefinition->getForms(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasForm
     */
    public function testHasForm(): void
    {
        self::assertTrue($this->blockDefinition->hasForm('content'));
        self::assertFalse($this->blockDefinition->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getForm
     */
    public function testGetForm(): void
    {
        self::assertSame(
            $this->form,
            $this->blockDefinition->getForm('content'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getForm
     */
    public function testGetFormThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Form "unknown" does not exist in "block_definition" block definition.');

        $this->blockDefinition->getForm('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        self::assertSame(
            [
                'collection' => $this->collection,
            ],
            $this->blockDefinition->getCollections(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasCollection
     */
    public function testHasCollection(): void
    {
        self::assertTrue($this->blockDefinition->hasCollection('collection'));
        self::assertFalse($this->blockDefinition->hasCollection('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getCollection
     */
    public function testGetCollection(): void
    {
        self::assertSame(
            $this->collection,
            $this->blockDefinition->getCollection('collection'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getCollection
     */
    public function testGetCollectionThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Collection "unknown" does not exist in "block_definition" block definition.');

        $this->blockDefinition->getCollection('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        self::assertSame(
            [
                'large' => $this->viewType1,
                'small' => $this->viewType2,
            ],
            $this->blockDefinition->getViewTypes(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers(): void
    {
        self::assertSame(
            ['large', 'small'],
            $this->blockDefinition->getViewTypeIdentifiers(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasViewType
     */
    public function testHasViewType(): void
    {
        self::assertTrue($this->blockDefinition->hasViewType('large'));
        self::assertFalse($this->blockDefinition->hasViewType('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getViewType
     */
    public function testGetViewType(): void
    {
        self::assertSame(
            $this->viewType1,
            $this->blockDefinition->getViewType('large'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getViewType
     */
    public function testGetViewTypeThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('View type "unknown" does not exist in "block_definition" block definition.');

        $this->blockDefinition->getViewType('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getDynamicParameters
     * @covers \Netgen\Layouts\Block\BlockDefinition::getHandler
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(3, $dynamicParameters);

        self::assertArrayHasKey('definition_param', $dynamicParameters);
        self::assertArrayHasKey('closure_param', $dynamicParameters);
        self::assertArrayHasKey('dynamic_param', $dynamicParameters);

        self::assertSame('definition_value', $dynamicParameters['definition_param']);
        self::assertSame('closure_value', $dynamicParameters['closure_param']);
        self::assertSame('dynamic_value', $dynamicParameters['dynamic_param']);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getHandler
     * @covers \Netgen\Layouts\Block\BlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertTrue($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getConfigDefinition
     */
    public function testGetConfigDefinition(): void
    {
        self::assertSame(
            $this->configDefinition,
            $this->blockDefinition->getConfigDefinition('config'),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getConfigDefinition
     */
    public function testGetConfigDefinitionThrowsConfigDefinitionException(): void
    {
        $this->expectException(ConfigDefinitionException::class);
        $this->expectExceptionMessage('Config definition with "unknown" config key does not exist.');

        $this->blockDefinition->getConfigDefinition('unknown');
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasConfigDefinition
     */
    public function testHasConfigDefinition(): void
    {
        self::assertTrue($this->blockDefinition->hasConfigDefinition('config'));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasConfigDefinition
     */
    public function testHasConfigDefinitionWithNonExistentDefinition(): void
    {
        self::assertFalse($this->blockDefinition->hasConfigDefinition('unknown'));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions(): void
    {
        self::assertSame(
            ['config' => $this->configDefinition],
            $this->blockDefinition->getConfigDefinitions(),
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasPlugin
     */
    public function testHasPlugin(): void
    {
        self::assertTrue($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getPlugin
     */
    public function testGetPlugin(): void
    {
        $this->expectNotToPerformAssertions();

        $this->blockDefinition->getPlugin(HandlerPlugin::class);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getPlugin
     */
    public function testGetPluginWithUnknownPlugin(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage(sprintf('Block definition with "%s" identifier does not have a plugin with "%s" class.', 'block_definition', stdClass::class));

        $this->blockDefinition->getPlugin(stdClass::class);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition::getPlugins
     */
    public function testGetPlugins(): void
    {
        $plugins = $this->blockDefinition->getPlugins();

        self::assertCount(1, $plugins);
        self::assertInstanceOf(HandlerPlugin::class, $plugins[0]);
    }
}
