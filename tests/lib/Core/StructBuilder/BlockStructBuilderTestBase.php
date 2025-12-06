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

    final protected function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new BlockStructBuilder(new ConfigStructBuilder());
    }

    final public function testNewBlockCreateStruct(): void
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition('title');

        $struct = $this->structBuilder->newBlockCreateStruct($blockDefinition);

        self::assertSame(
            [
                'collectionCreateStructs' => [],
                'configStructs' => [],
                'definition' => $blockDefinition,
                'isAlwaysAvailable' => true,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'name' => '',
                'parameterValues' => [
                    'tag' => 'h1',
                    'title' => 'Title',
                    'use_link' => null,
                    'link' => null,
                ],
                'viewType' => 'standard',
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewBlockUpdateStruct(): void
    {
        $struct = $this->structBuilder->newBlockUpdateStruct('en');

        self::assertSame(
            [
                'configStructs' => [],
                'isAlwaysAvailable' => null,
                'itemViewType' => null,
                'locale' => 'en',
                'name' => null,
                'parameterValues' => [],
                'viewType' => null,
            ],
            $this->exportObject($struct),
        );
    }

    final public function testNewBlockUpdateStructFromBlock(): void
    {
        $block = $this->blockService->loadBlockDraft(Uuid::fromString('b40aa688-b8e8-5e07-bf82-4a97e5ed8bad'));
        $struct = $this->structBuilder->newBlockUpdateStruct('en', $block);

        self::assertArrayHasKey('key', $struct->configStructs);

        self::assertSame(
            [
                'configStructs' => [
                    'key' => [
                        'parameterValues' => [
                            'param1' => null,
                            'param2' => null,
                        ],
                    ],
                ],
                'isAlwaysAvailable' => true,
                'itemViewType' => 'standard',
                'locale' => 'en',
                'name' => 'My sixth block',
                'parameterValues' => [
                    'tag' => 'h3',
                    'title' => 'Title',
                    'use_link' => null,
                    'link' => null,
                ],
                'viewType' => 'title',
            ],
            $this->exportObject($struct, true),
        );
    }
}
