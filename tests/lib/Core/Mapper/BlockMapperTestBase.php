<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\Block\NullBlockDefinition;
use Netgen\Layouts\Core\Mapper\BlockMapper;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Persistence\Values\Block\Block;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
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
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => PersistenceStatus::Published,
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
            $block->definition,
        );

        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $block->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $block->layoutId->toString());
        self::assertSame('default', $block->viewType);
        self::assertSame('standard', $block->itemViewType);
        self::assertSame('My block', $block->name);
        self::assertSame(3, $block->position);
        self::assertInstanceOf(UuidInterface::class, $block->parentBlockId);
        self::assertSame('cbca9628-3ff1-5440-b1c3-0018331d3544', $block->parentBlockId->toString());
        self::assertSame('main', $block->parentPlaceholder);
        self::assertTrue($block->isPublished);
        self::assertSame('test', $block->getParameter('css_class')->value);
        self::assertNull($block->getParameter('css_id')->value);
        self::assertTrue($block->hasConfig('key'));

        $blockConfig = $block->getConfig('key');

        self::assertTrue($blockConfig->getParameter('param1')->value);
        self::assertSame(400, $blockConfig->getParameter('param2')->value);

        self::assertTrue($block->isTranslatable);
        self::assertSame('en', $block->mainLocale);
        self::assertFalse($block->isAlwaysAvailable);
        self::assertSame(['en'], $block->availableLocales);

        self::assertSame('en', $block->locale);

        self::assertSame('test', $block->getParameter('css_class')->value);
        self::assertNull($block->getParameter('css_id')->value);
    }

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
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => PersistenceStatus::Published,
                'parameters' => ['en' => []],
                'config' => [],
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock);

        self::assertSame(3, $block->position);
        self::assertNull($block->parentBlockId);
        self::assertNull($block->parentPlaceholder);
    }

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
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => PersistenceStatus::Published,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock, ['hr']);

        self::assertSame(['en', 'hr', 'de'], $block->availableLocales);
        self::assertSame('hr', $block->locale);
    }

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
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => PersistenceStatus::Published,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock, ['hr', 'en']);

        self::assertSame(['en', 'hr', 'de'], $block->availableLocales);
        self::assertSame('hr', $block->locale);
    }

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
                'isAlwaysAvailable' => true,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => PersistenceStatus::Published,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
        );

        $block = $this->mapper->mapBlock($persistenceBlock, ['fr', 'no']);

        self::assertSame(['en', 'hr', 'de'], $block->availableLocales);
        self::assertSame('en', $block->locale);
    }

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
                'isAlwaysAvailable' => true,
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'status' => PersistenceStatus::Published,
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
        );

        $this->mapper->mapBlock($persistenceBlock, ['fr', 'no'], false);
    }

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
                'isAlwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ],
        );

        $this->mapper->mapBlock($persistenceBlock, ['fr', 'no']);
    }

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
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => PersistenceStatus::Published,
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

        self::assertInstanceOf(NullBlockDefinition::class, $block->definition);

        self::assertSame('28df256a-2467-5527-b398-9269ccc652de', $block->id->toString());
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $block->layoutId->toString());
        self::assertSame('default', $block->viewType);
        self::assertSame('standard', $block->itemViewType);
        self::assertSame('My block', $block->name);
        self::assertSame(3, $block->position);
        self::assertTrue($block->isPublished);

        self::assertFalse($block->hasParameter('css_class'));
        self::assertFalse($block->hasParameter('css_id'));

        self::assertFalse($block->hasConfig('key'));

        self::assertTrue($block->isTranslatable);
        self::assertSame('en', $block->mainLocale);
        self::assertFalse($block->isAlwaysAvailable);
        self::assertSame(['en'], $block->availableLocales);

        self::assertSame('en', $block->locale);
    }

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
                'status' => PersistenceStatus::Published,
                'name' => 'My block',
                'depth' => 1,
                'position' => 3,
                'placeholder' => 'main',
                'isTranslatable' => true,
                'isAlwaysAvailable' => false,
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
            $block->definition,
        );

        self::assertTrue($block->hasPlaceholder('left'));

        $placeholder = $block->getPlaceholder('left');
        self::assertSame('left', $placeholder->identifier);
        self::assertCount(1, $placeholder->blocks);
        self::assertTrue($block->hasPlaceholder('right'));

        $placeholder = $block->getPlaceholder('right');
        self::assertSame('right', $placeholder->identifier);
        self::assertCount(0, $placeholder->blocks);
    }

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
                'isAlwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'status' => PersistenceStatus::Published,
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
            $block->definition,
        );

        self::assertTrue($block->hasCollection('default'));
        self::assertTrue($block->hasCollection('featured'));
    }
}
