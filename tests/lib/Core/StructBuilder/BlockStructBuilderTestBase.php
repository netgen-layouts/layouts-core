<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\Core\StructBuilder\BlockStructBuilder;
use Netgen\Layouts\Core\StructBuilder\ConfigStructBuilder;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class BlockStructBuilderTestBase extends CoreTestCase
{
    use ExportObjectTrait;

    private BlockStructBuilder $structBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new BlockStructBuilder(new ConfigStructBuilder());
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\BlockStructBuilder::__construct
     * @covers \Netgen\Layouts\Core\StructBuilder\BlockStructBuilder::newBlockCreateStruct
     */
    public function testNewBlockCreateStruct(): void
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $struct = $this->structBuilder->newBlockCreateStruct($blockDefinition);

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'collectionCreateStructs' => [],
                'configStructs' => [],
                'definition' => $blockDefinition,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'name' => '',
                'parameterValues' => [
                    'css_class' => 'some-class',
                    'css_id' => null,
                ],
                'viewType' => 'small',
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\BlockStructBuilder::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStruct(): void
    {
        $struct = $this->structBuilder->newBlockUpdateStruct('en');

        self::assertSame(
            [
                'alwaysAvailable' => null,
                'configStructs' => [],
                'itemViewType' => null,
                'locale' => 'en',
                'name' => null,
                'parameterValues' => [],
                'viewType' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\BlockStructBuilder::newBlockUpdateStruct
     */
    public function testNewBlockUpdateStructFromBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('b40aa688-b8e8-5e07-bf82-4a97e5ed8bad'));
        $struct = $this->structBuilder->newBlockUpdateStruct('en', $block);

        self::assertArrayHasKey('key', $struct->getConfigStructs());

        self::assertSame(
            [
                'alwaysAvailable' => true,
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
                'itemViewType' => 'standard',
                'locale' => 'en',
                'name' => 'My sixth block',
                'parameterValues' => [
                    'css_class' => 'CSS class',
                    'css_id' => null,
                ],
                'viewType' => 'title',
            ],
            $this->exportObject($struct, true),
        );
    }
}
