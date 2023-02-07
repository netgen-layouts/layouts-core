<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Core\Mapper\BlockMapper;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Ramsey\Uuid\UuidInterface;

abstract class BlockMapperTestBase extends CoreTestCase
{
    private BlockMapper $mapper;

    protected function setUp(): void
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
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 2,
                'position' => 3,
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
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
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition(),
        );

        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $block->getId()->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $block->getLayoutId()->toString());
        self::assertSame('default', $block->getViewType());
        self::assertSame('standard', $block->getItemViewType());
        self::assertSame('My block', $block->getName());
        self::assertSame(3, $block->getPosition());
        self::assertInstanceOf(UuidInterface::class, $block->getParentBlockId());
        self::assertSame('cbca9628-3ff1-5440-b1c3-0018331d3544', $block->getParentBlockId()->toString());
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
                'id' => 31,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'placeholder' => 'main',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => ['en' => []],
                'config' => [],
            ],
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
                'id' => 31,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
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
                'id' => 31,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
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
                'id' => 31,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'alwaysAvailable' => true,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
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
        $this->expectExceptionMessage('Could not find block with identifier "28df256a-2467-5527-b398-9269ccc652de"');

        $persistenceBlock = Block::fromArray(
            [
                'id' => 31,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
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
        $this->expectExceptionMessage('Could not find block with identifier "28df256a-2467-5527-b398-9269ccc652de"');

        $persistenceBlock = Block::fromArray(
            [
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
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
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'unknown',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentId' => 13,
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'placeholder' => 'left',
                'depth' => 2,
                'path' => '/13/31/',
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
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertInstanceOf(NullBlockDefinition::class, $block->getDefinition());

        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $block->getId()->toString());
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
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
                'definitionIdentifier' => 'two_columns',
                'status' => Value::STATUS_PUBLISHED,
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'isTranslatable' => true,
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
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('two_columns'),
            $block->getDefinition(),
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
     * @covers \Netgen\Layouts\Core\Mapper\BlockMapper::mapBlock
     */
    public function testMapBlockWithCollections(): void
    {
        $persistenceBlock = Block::fromArray(
            [
                'id' => 31,
                'uuid' => '28df256a-2467-5527-b398-9269ccc652de',
                'layoutId' => 13,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'parentUuid' => 'cbca9628-3ff1-5440-b1c3-0018331d3544',
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
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition(),
        );

        self::assertTrue($block->hasCollection('default'));
        self::assertTrue($block->hasCollection('featured'));
    }
}
