<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\Core\StructBuilder\BlockStructBuilder;
use Netgen\Layouts\Core\StructBuilder\ConfigStructBuilder;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use Ramsey\Uuid\Uuid;

abstract class BlockStructBuilderTest extends CoreTestCase
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
                'viewType' => 'small',
                'itemViewType' => 'standard',
                'name' => '',
                'isTranslatable' => true,
                'alwaysAvailable' => true,
                'definition' => $blockDefinition,
                'collectionCreateStructs' => [],
                'configStructs' => [],
                'parameterValues' => [
                    'css_class' => 'some-class',
                    'css_id' => null,
                ],
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
                'locale' => 'en',
                'viewType' => null,
                'itemViewType' => null,
                'name' => null,
                'alwaysAvailable' => null,
                'configStructs' => [],
                'parameterValues' => [],
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
                'locale' => 'en',
                'viewType' => 'title',
                'itemViewType' => 'standard',
                'name' => 'My sixth block',
                'alwaysAvailable' => true,
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
                'parameterValues' => [
                    'css_class' => 'CSS class',
                    'css_id' => null,
                ],
            ],
            $this->exportObject($struct, true),
        );
    }
}
