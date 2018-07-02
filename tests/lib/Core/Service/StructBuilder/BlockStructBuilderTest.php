<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Block\BlockCreateStruct;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder;
use Netgen\BlockManager\Core\Service\StructBuilder\ConfigStructBuilder;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectTrait;

abstract class BlockStructBuilderTest extends ServiceTestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->blockService = $this->createBlockService();

        $this->structBuilder = new BlockStructBuilder(
            new ConfigStructBuilder()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder::__construct
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct(): void
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $struct = $this->structBuilder->newBlockCreateStruct($blockDefinition);

        $this->assertInstanceOf(BlockCreateStruct::class, $struct);

        $this->assertSame(
            [
                'definition' => $blockDefinition,
                'viewType' => 'small',
                'itemViewType' => 'standard',
                'name' => null,
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'collectionCreateStructs' => [],
                'parameterValues' => [
                    'css_class' => 'some-class',
                    'css_id' => null,
                ],
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct(): void
    {
        $struct = $this->structBuilder->newBlockUpdateStruct('en');

        $this->assertInstanceOf(BlockUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'locale' => 'en',
                'viewType' => null,
                'itemViewType' => null,
                'name' => null,
                'alwaysAvailable' => null,
                'parameterValues' => [],
                'configStructs' => [],
            ],
            $this->exportObject($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\BlockStructBuilder::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(36);
        $struct = $this->structBuilder->newBlockUpdateStruct('en', $block);

        $this->assertInstanceOf(BlockUpdateStruct::class, $struct);

        $this->assertArrayHasKey('key', $struct->getConfigStructs());
        $this->assertInstanceOf(ConfigStruct::class, $struct->getConfigStruct('key'));

        $this->assertSame(
            [
                'locale' => 'en',
                'viewType' => 'title',
                'itemViewType' => 'standard',
                'name' => 'My sixth block',
                'alwaysAvailable' => true,
                'parameterValues' => [
                    'css_class' => 'CSS class',
                    'css_id' => null,
                ],
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
            ],
            $this->exportObject($struct, true)
        );
    }
}
