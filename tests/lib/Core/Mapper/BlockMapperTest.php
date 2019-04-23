<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class BlockMapperTest extends CoreTestCase
{
    /**
     * @var \Netgen\Layouts\Core\Mapper\BlockMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createBlockMapper();
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlock(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'id' => 31,
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 2,
                'position' => 3,
                'parentId' => 42,
                'placeholder' => 'main',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => [
                    'en' => [
                        'css_class' => 'test',
                        'some_param' => 'some_value',
                    ],
                ],
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 400,
                    ],
                ],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition()
        );

        self::assertSame(31, $block->getId());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $block->getLayoutId()->toString());
        self::assertSame('default', $block->getViewType());
        self::assertSame('standard', $block->getItemViewType());
        self::assertSame('My block', $block->getName());
        self::assertSame(3, $block->getPosition());
        self::assertSame(42, $block->getParentBlockId());
        self::assertSame('main', $block->getParentPlaceholder());
        self::assertTrue($block->isPublished());
        self::assertSame('test', $block->getParameter('css_class')->getValue());
        self::assertNull($block->getParameter('css_id')->getValue());
        self::assertTrue($block->hasConfig('key'));

        $blockConfig = $block->getConfig('key');

        self::assertTrue($blockConfig->getParameter('param1')->getValue());
        self::assertSame(400, $blockConfig->getParameter('param2')->getValue());

        self::assertTrue($block->isTranslatable());
        self::assertSame('en', $block->getMainLocale());
        self::assertFalse($block->isAlwaysAvailable());
        self::assertSame(['en'], $block->getAvailableLocales());

        self::assertSame('en', $block->getLocale());

        self::assertSame('test', $block->getParameter('css_class')->getValue());
        self::assertNull($block->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     */
    public function testMapBlockWithNoParent(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'depth' => 1,
                'position' => 3,
                'parentId' => 42,
                'placeholder' => 'main',
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => ['en' => []],
                'config' => [],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(3, $block->getPosition());
        self::assertNull($block->getParentBlockId());
        self::assertNull($block->getParentPlaceholder());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocale(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock, ['hr']);

        self::assertSame(['en', 'hr', 'de'], $block->getAvailableLocales());
        self::assertSame('hr', $block->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocales(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock, ['hr', 'en']);

        self::assertSame(['en', 'hr', 'de'], $block->getAvailableLocales());
        self::assertSame('hr', $block->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock, ['fr', 'no']);

        self::assertSame(['en', 'hr', 'de'], $block->getAvailableLocales());
        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "42"');

        $persistenceBlock = Block::fromArray(
            [
                'id' => 42,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $this->mapper->mapBlock($persistenceBlock, ['fr', 'no'], false);
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocalesAndNotAlwaysAvailable(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Could not find block with identifier "42"');

        $persistenceBlock = Block::fromArray(
            [
                'id' => 42,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $this->mapper->mapBlock($persistenceBlock, ['fr', 'no']);
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     */
    public function testMapBlockWithInvalidDefinition(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'id' => 31,
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'unknown',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'position' => 3,
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => [
                    'en' => [
                        'css_class' => 'test',
                        'some_param' => 'some_value',
                    ],
                ],
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 400,
                    ],
                ],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertInstanceOf(NullBlockDefinition::class, $block->getDefinition());

        self::assertSame(31, $block->getId());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $block->getLayoutId()->toString());
        self::assertSame('default', $block->getViewType());
        self::assertSame('standard', $block->getItemViewType());
        self::assertSame('My block', $block->getName());
        self::assertSame(3, $block->getPosition());
        self::assertTrue($block->isPublished());

        self::assertFalse($block->hasParameter('css_class'));
        self::assertFalse($block->hasParameter('css_id'));

        self::assertFalse($block->hasConfig('key'));

        self::assertTrue($block->isTranslatable());
        self::assertSame('en', $block->getMainLocale());
        self::assertFalse($block->isAlwaysAvailable());
        self::assertSame(['en'], $block->getAvailableLocales());

        self::assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapContainerBlock(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'id' => 33,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'two_columns',
                'status' => Value::STATUS_PUBLISHED,
                'name' => 'My block',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => ['en' => []],
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 400,
                    ],
                ],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('two_columns'),
            $block->getDefinition()
        );

        self::assertTrue($block->hasPlaceholder('left'));

        $placeholder = $block->getPlaceholder('left');
        self::assertSame('left', $placeholder->getIdentifier());
        self::assertCount(1, $placeholder->getBlocks());
        self::assertTrue($block->hasPlaceholder('right'));

        $placeholder = $block->getPlaceholder('right');
        self::assertSame('right', $placeholder->getIdentifier());
        self::assertCount(0, $placeholder->getBlocks());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::loadCollections
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     */
    public function testMapBlockWithCollections(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'id' => 31,
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => [
                    'en' => [
                        'css_class' => 'test',
                        'some_param' => 'some_value',
                    ],
                ],
                'config' => [
                    'key' => [
                        'param1' => true,
                        'param2' => 400,
                    ],
                ],
            ]
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition()
        );

        self::assertTrue($block->hasCollection('default'));
        self::assertTrue($block->hasCollection('featured'));
    }
}
