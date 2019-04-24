<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Block\CollectionReference;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class BlockMapperTest extends TestCase
{
    use ExportObjectTrait;

    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new BlockMapper();
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper::buildParameters
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper::mapBlocks
     */
    public function testMapBlocks(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'layout_id' => '24',
                'depth' => '1',
                'path' => '/22/42/',
                'parent_id' => '22',
                'placeholder' => 'root',
                'position' => '4',
                'definition_identifier' => 'text',
                'parameters' => '{"param1": "param2"}',
                'config' => '{"config1": "config2"}',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'name' => 'My block',
                'locale' => 'en',
                'translatable' => '0',
                'main_locale' => 'en',
                'always_available' => '1',
                'status' => '1',
                'layout_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
            [
                'id' => 84,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'layout_id' => 48,
                'depth' => 1,
                'path' => '/23/84/',
                'parent_id' => 23,
                'placeholder' => 'root',
                'position' => 3,
                'definition_identifier' => 'title',
                'parameters' => '{"param1": 42}',
                'config' => '{"config1": 42}',
                'view_type' => 'small',
                'item_view_type' => 'standard',
                'name' => 'My other block',
                'translatable' => true,
                'locale' => 'en',
                'main_locale' => 'en',
                'always_available' => true,
                'status' => Value::STATUS_PUBLISHED,
                'layout_uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'layoutId' => 24,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'depth' => 1,
                'path' => '/22/42/',
                'parentId' => 22,
                'placeholder' => 'root',
                'position' => 4,
                'definitionIdentifier' => 'text',
                'parameters' => [
                    'en' => [
                        'param1' => 'param2',
                    ],
                ],
                'config' => [
                    'config1' => 'config2',
                ],
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
            [
                'id' => 84,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'layoutId' => 48,
                'layoutUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'depth' => 1,
                'path' => '/23/84/',
                'parentId' => 23,
                'placeholder' => 'root',
                'position' => 3,
                'definitionIdentifier' => 'title',
                'parameters' => [
                    'en' => [
                        'param1' => 42,
                    ],
                ],
                'config' => [
                    'config1' => 42,
                ],
                'viewType' => 'small',
                'itemViewType' => 'standard',
                'name' => 'My other block',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $blocks = $this->mapper->mapBlocks($data);

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);
        self::assertSame($expectedData, $this->exportObjectList($blocks));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper::buildParameters
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper::mapBlocks
     */
    public function testMapBlocksWithLayoutUuid(): void
    {
        $data = [
            [
                'id' => '42',
                'uuid' => '01f0c14e-2e15-54a1-8b41-58a3a8a9a917',
                'layout_id' => '24',
                'depth' => '1',
                'path' => '/22/42/',
                'parent_id' => '22',
                'placeholder' => 'root',
                'position' => '4',
                'definition_identifier' => 'text',
                'parameters' => '{"param1": "param2"}',
                'config' => '{"config1": "config2"}',
                'view_type' => 'default',
                'item_view_type' => 'standard',
                'name' => 'My block',
                'locale' => 'en',
                'translatable' => '0',
                'main_locale' => 'en',
                'always_available' => '1',
                'status' => '1',
                'layout_uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
            ],
        ];

        $expectedData = [
            [
                'id' => 42,
                'uuid' => '01f0c14e-2e15-54a1-8b41-58a3a8a9a917',
                'layoutId' => 24,
                'layoutUuid' => 'abcdef01-abcd-abcd-abcd-0123456789ab',
                'depth' => 1,
                'path' => '/22/42/',
                'parentId' => 22,
                'placeholder' => 'root',
                'position' => 4,
                'definitionIdentifier' => 'text',
                'parameters' => [
                    'en' => [
                        'param1' => 'param2',
                    ],
                ],
                'config' => [
                    'config1' => 'config2',
                ],
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'isTranslatable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'alwaysAvailable' => true,
                'status' => Value::STATUS_PUBLISHED,
            ],
        ];

        $blocks = $this->mapper->mapBlocks($data, 'abcdef01-abcd-abcd-abcd-0123456789ab');

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);
        self::assertSame($expectedData, $this->exportObjectList($blocks));
    }

    /**
     * @covers \Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper::mapCollectionReferences
     */
    public function testMapCollectionReferences(): void
    {
        $data = [
            [
                'block_id' => '1',
                'block_status' => '1',
                'collection_id' => '42',
                'collection_status' => '1',
                'identifier' => 'default',
            ],
            [
                'block_id' => 2,
                'block_status' => Value::STATUS_PUBLISHED,
                'collection_id' => 43,
                'collection_status' => Value::STATUS_PUBLISHED,
                'identifier' => 'featured',
            ],
        ];

        $expectedData = [
            [
                'blockId' => 1,
                'blockStatus' => Value::STATUS_PUBLISHED,
                'collectionId' => 42,
                'collectionStatus' => Value::STATUS_PUBLISHED,
                'identifier' => 'default',
            ],
            [
                'blockId' => 2,
                'blockStatus' => Value::STATUS_PUBLISHED,
                'collectionId' => 43,
                'collectionStatus' => Value::STATUS_PUBLISHED,
                'identifier' => 'featured',
            ],
        ];

        $collectionReferences = $this->mapper->mapCollectionReferences($data);

        self::assertContainsOnlyInstancesOf(CollectionReference::class, $collectionReferences);
        self::assertSame($expectedData, $this->exportObjectList($collectionReferences));
    }
}
