<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Exception\Block\BlockDefinitionException;
use Netgen\BlockManager\Exception\Config\ConfigDefinitionException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\Tests\Block\Stubs\HandlerPlugin;
use PHPUnit\Framework\TestCase;
use stdClass;

final class BlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    private $blockDefinition;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    private $form;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection
     */
    private $collection;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    private $viewType1;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    private $viewType2;

    /**
     * @var ConfigDefinition
     */
    private $configDefinition;

    public function setUp(): void
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
                'viewTypes' => [
                    'large' => $this->viewType1,
                    'small' => $this->viewType2,
                ],
                'configDefinitions' => ['config' => $this->configDefinition],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('block_definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getName
     */
    public function testGetName(): void
    {
        self::assertSame('Block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        self::assertSame('/icon.svg', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isTranslatable
     */
    public function testIsTranslatable(): void
    {
        self::assertTrue($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getForms
     */
    public function testGetForms(): void
    {
        self::assertSame(
            [
                'content' => $this->form,
            ],
            $this->blockDefinition->getForms()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasForm
     */
    public function testHasForm(): void
    {
        self::assertTrue($this->blockDefinition->hasForm('content'));
        self::assertFalse($this->blockDefinition->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getForm
     */
    public function testGetForm(): void
    {
        self::assertSame(
            $this->form,
            $this->blockDefinition->getForm('content')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getForm
     */
    public function testGetFormThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Form "unknown" does not exist in "block_definition" block definition.');

        $this->blockDefinition->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        self::assertSame(
            [
                'collection' => $this->collection,
            ],
            $this->blockDefinition->getCollections()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasCollection
     */
    public function testHasCollection(): void
    {
        self::assertTrue($this->blockDefinition->hasCollection('collection'));
        self::assertFalse($this->blockDefinition->hasCollection('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getCollection
     */
    public function testGetCollection(): void
    {
        self::assertSame(
            $this->collection,
            $this->blockDefinition->getCollection('collection')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getCollection
     */
    public function testGetCollectionThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('Collection "unknown" does not exist in "block_definition" block definition.');

        $this->blockDefinition->getCollection('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        self::assertSame(
            [
                'large' => $this->viewType1,
                'small' => $this->viewType2,
            ],
            $this->blockDefinition->getViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewTypeIdentifiers
     */
    public function testGetViewTypeIdentifiers(): void
    {
        self::assertSame(
            ['large', 'small'],
            $this->blockDefinition->getViewTypeIdentifiers()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasViewType
     */
    public function testHasViewType(): void
    {
        self::assertTrue($this->blockDefinition->hasViewType('large'));
        self::assertFalse($this->blockDefinition->hasViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewType
     */
    public function testGetViewType(): void
    {
        self::assertSame(
            $this->viewType1,
            $this->blockDefinition->getViewType('large')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewType
     */
    public function testGetViewTypeThrowsBlockDefinitionException(): void
    {
        $this->expectException(BlockDefinitionException::class);
        $this->expectExceptionMessage('View type "unknown" does not exist in "block_definition" block definition.');

        $this->blockDefinition->getViewType('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $dynamicParameters['definition_param'] = 'definition_value';
        $dynamicParameters['closure_param'] = static function (): string {
            return 'closure_value';
        };

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
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertTrue($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinition
     */
    public function testGetConfigDefinition(): void
    {
        self::assertSame(
            $this->configDefinition,
            $this->blockDefinition->getConfigDefinition('config')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinition
     */
    public function testGetConfigDefinitionThrowsConfigDefinitionException(): void
    {
        $this->expectException(ConfigDefinitionException::class);
        $this->expectExceptionMessage('Config definition with "unknown" config key does not exist.');

        $this->blockDefinition->getConfigDefinition('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasConfigDefinition
     */
    public function testHasConfigDefinition(): void
    {
        self::assertTrue($this->blockDefinition->hasConfigDefinition('config'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasConfigDefinition
     */
    public function testHasConfigDefinitionWithNonExistentDefinition(): void
    {
        self::assertFalse($this->blockDefinition->hasConfigDefinition('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions(): void
    {
        self::assertSame(
            ['config' => $this->configDefinition],
            $this->blockDefinition->getConfigDefinitions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasPlugin
     */
    public function testHasPlugin(): void
    {
        self::assertTrue($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin(): void
    {
        self::assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }
}
