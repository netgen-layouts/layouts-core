<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Collection;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
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

        $this->form = new Form(['identifier' => 'content']);
        $this->collection = new Collection(['identifier' => 'collection']);
        $this->configDefinition = new ConfigDefinition();

        $this->viewType1 = new ViewType(['identifier' => 'large']);
        $this->viewType2 = new ViewType(['identifier' => 'small']);

        $this->blockDefinition = new BlockDefinition(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'handlerPlugins' => [HandlerPlugin::instance()],
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
        $this->assertSame('block_definition', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getName
     */
    public function testGetName(): void
    {
        $this->assertSame('Block definition', $this->blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getIcon
     */
    public function testGetIcon(): void
    {
        $this->assertSame('/icon.svg', $this->blockDefinition->getIcon());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isTranslatable
     */
    public function testIsTranslatable(): void
    {
        $this->assertTrue($this->blockDefinition->isTranslatable());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getForms
     */
    public function testGetForms(): void
    {
        $this->assertSame(
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
        $this->assertTrue($this->blockDefinition->hasForm('content'));
        $this->assertFalse($this->blockDefinition->hasForm('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getForm
     */
    public function testGetForm(): void
    {
        $this->assertSame(
            $this->form,
            $this->blockDefinition->getForm('content')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getForm
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Form "unknown" does not exist in "block_definition" block definition.
     */
    public function testGetFormThrowsBlockDefinitionException(): void
    {
        $this->blockDefinition->getForm('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getCollections
     */
    public function testGetCollections(): void
    {
        $this->assertSame(
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
        $this->assertTrue($this->blockDefinition->hasCollection('collection'));
        $this->assertFalse($this->blockDefinition->hasCollection('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getCollection
     */
    public function testGetCollection(): void
    {
        $this->assertSame(
            $this->collection,
            $this->blockDefinition->getCollection('collection')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getCollection
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage Collection "unknown" does not exist in "block_definition" block definition.
     */
    public function testGetCollectionThrowsBlockDefinitionException(): void
    {
        $this->blockDefinition->getCollection('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewTypes
     */
    public function testGetViewTypes(): void
    {
        $this->assertSame(
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
        $this->assertSame(
            ['large', 'small'],
            $this->blockDefinition->getViewTypeIdentifiers()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasViewType
     */
    public function testHasViewType(): void
    {
        $this->assertTrue($this->blockDefinition->hasViewType('large'));
        $this->assertFalse($this->blockDefinition->hasViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewType
     */
    public function testGetViewType(): void
    {
        $this->assertSame(
            $this->viewType1,
            $this->blockDefinition->getViewType('large')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getViewType
     * @expectedException \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     * @expectedExceptionMessage View type "unknown" does not exist in "block_definition" block definition.
     */
    public function testGetViewTypeThrowsBlockDefinitionException(): void
    {
        $this->blockDefinition->getViewType('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = new DynamicParameters();
        $dynamicParameters['definition_param'] = 'definition_value';
        $dynamicParameters['closure_param'] = function (): string {
            return 'closure_value';
        };

        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        $this->assertCount(3, $dynamicParameters);

        $this->assertArrayHasKey('definition_param', $dynamicParameters);
        $this->assertArrayHasKey('closure_param', $dynamicParameters);
        $this->assertArrayHasKey('dynamic_param', $dynamicParameters);

        $this->assertSame('definition_value', $dynamicParameters['definition_param']);
        $this->assertSame('closure_value', $dynamicParameters['closure_param']);
        $this->assertSame('dynamic_value', $dynamicParameters['dynamic_param']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        $this->assertTrue($this->blockDefinition->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinition
     */
    public function testGetConfigDefinition(): void
    {
        $this->assertSame(
            $this->configDefinition,
            $this->blockDefinition->getConfigDefinition('config')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinition
     * @expectedException \Netgen\BlockManager\Exception\Config\ConfigDefinitionException
     * @expectedExceptionMessage Config definition with "unknown" config key does not exist.
     */
    public function testGetConfigDefinitionThrowsConfigDefinitionException(): void
    {
        $this->blockDefinition->getConfigDefinition('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinition
     */
    public function testHasConfigDefinition(): void
    {
        $this->assertTrue($this->blockDefinition->hasConfigDefinition('config'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinition
     */
    public function testHasConfigDefinitionWithNonExistentDefinition(): void
    {
        $this->assertFalse($this->blockDefinition->hasConfigDefinition('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfigDefinitions
     */
    public function testGetConfigDefinitions(): void
    {
        $this->assertSame(
            ['config' => $this->configDefinition],
            $this->blockDefinition->getConfigDefinitions()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasPlugin
     */
    public function testHasPlugin(): void
    {
        $this->assertTrue($this->blockDefinition->hasPlugin(HandlerPlugin::class));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::hasPlugin
     */
    public function testHasPluginWithUnknownPlugin(): void
    {
        $this->assertFalse($this->blockDefinition->hasPlugin(stdClass::class));
    }
}
