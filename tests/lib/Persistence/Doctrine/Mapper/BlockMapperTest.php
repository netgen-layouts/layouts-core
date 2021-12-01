<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Doctrine\Mapper;

use Netgen\Layouts\Persistence\Doctrine\Mapper\BlockMapper;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;
use PHPUnit\Framework\TestCase;

final class BlockMapperTest extends TestCase
{
    use ExportObjectTrait;

    private BlockMapper $mapper;

    protected function setUp(): void
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
                'parent_uuid' => 'fdcd2719-9192-58bf-9f02-18242ff9199c',
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
                'layout_uuid' => 'd95effa2-37ee-5a5f-bd13-40bf179356ef',
            ],
            [
                'id' => 84,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'layout_id' => 48,
                'depth' => 1,
                'path' => '/23/84/',
                'parent_id' => 23,
                'parent_uuid' => 'dba8bd91-b60c-5311-879b-87a7f8f5f017',
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
                'layout_uuid' => '63e2737f-c31f-594a-bd46-2172f117f650',
            ],
        ];

        $expectedData = [
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'config' => [
                    'config1' => 'config2',
                ],
                'definitionIdentifier' => 'text',
                'depth' => 1,
                'id' => 42,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 24,
                'layoutUuid' => 'd95effa2-37ee-5a5f-bd13-40bf179356ef',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'param1' => 'param2',
                    ],
                ],
                'parentId' => 22,
                'parentUuid' => 'fdcd2719-9192-58bf-9f02-18242ff9199c',
                'path' => '/22/42/',
                'placeholder' => 'root',
                'position' => 4,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'default',
            ],
            [
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'config' => [
                    'config1' => 42,
                ],
                'definitionIdentifier' => 'title',
                'depth' => 1,
                'id' => 84,
                'isTranslatable' => true,
                'itemViewType' => 'standard',
                'layoutId' => 48,
                'layoutUuid' => '63e2737f-c31f-594a-bd46-2172f117f650',
                'mainLocale' => 'en',
                'name' => 'My other block',
                'parameters' => [
                    'en' => [
                        'param1' => 42,
                    ],
                ],
                'parentId' => 23,
                'parentUuid' => 'dba8bd91-b60c-5311-879b-87a7f8f5f017',
                'path' => '/23/84/',
                'placeholder' => 'root',
                'position' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'viewType' => 'small',
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
                'parent_uuid' => 'fdcd2719-9192-58bf-9f02-18242ff9199c',
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
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'config' => [
                    'config1' => 'config2',
                ],
                'definitionIdentifier' => 'text',
                'depth' => 1,
                'id' => 42,
                'isTranslatable' => false,
                'itemViewType' => 'standard',
                'layoutId' => 24,
                'layoutUuid' => 'abcdef01-abcd-abcd-abcd-0123456789ab',
                'mainLocale' => 'en',
                'name' => 'My block',
                'parameters' => [
                    'en' => [
                        'param1' => 'param2',
                    ],
                ],
                'parentId' => 22,
                'parentUuid' => 'fdcd2719-9192-58bf-9f02-18242ff9199c',
                'path' => '/22/42/',
                'placeholder' => 'root',
                'position' => 4,
                'status' => Value::STATUS_PUBLISHED,
                'uuid' => '01f0c14e-2e15-54a1-8b41-58a3a8a9a917',
                'viewType' => 'default',
            ],
        ];

        $blocks = $this->mapper->mapBlocks($data, 'abcdef01-abcd-abcd-abcd-0123456789ab');

        self::assertContainsOnlyInstancesOf(Block::class, $blocks);
        self::assertSame($expectedData, $this->exportObjectList($blocks));
    }
}
