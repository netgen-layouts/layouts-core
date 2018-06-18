<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\NullBlockDefinition;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class BlockMapperTest extends ServiceTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->blockMapper = $this->createBlockMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlock(): void
    {
        $persistenceBlock = new Block(
            [
                'id' => 31,
                'layoutId' => 13,
                'definitionIdentifier' => 'text',
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
                    'http_cache' => [
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ],
                ],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition()
        );

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertSame(31, $block->getId());
        $this->assertSame(13, $block->getLayoutId());
        $this->assertSame('default', $block->getViewType());
        $this->assertSame('standard', $block->getItemViewType());
        $this->assertSame('My block', $block->getName());
        $this->assertSame(3, $block->getParentPosition());
        $this->assertTrue($block->isPublished());

        $this->assertSame('test', $block->getParameter('css_class')->getValue());
        $this->assertNull($block->getParameter('css_id')->getValue());

        $this->assertTrue($block->hasConfig('http_cache'));
        $this->assertInstanceOf(Config::class, $block->getConfig('http_cache'));

        $httpCacheConfig = $block->getConfig('http_cache');

        $this->assertTrue($httpCacheConfig->getParameter('use_http_cache')->getValue());
        $this->assertSame(400, $httpCacheConfig->getParameter('shared_max_age')->getValue());

        $this->assertTrue($block->isTranslatable());
        $this->assertSame('en', $block->getMainLocale());
        $this->assertFalse($block->isAlwaysAvailable());
        $this->assertSame(['en'], $block->getAvailableLocales());

        $this->assertSame('en', $block->getLocale());

        $this->assertSame('test', $block->getParameter('css_class')->getValue());
        $this->assertNull($block->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocale(): void
    {
        $persistenceBlock = new Block(
            [
                'definitionIdentifier' => 'text',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock, ['hr']);

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertSame(['en', 'hr', 'de'], $block->getAvailableLocales());
        $this->assertSame('hr', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocales(): void
    {
        $persistenceBlock = new Block(
            [
                'definitionIdentifier' => 'text',
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock, ['hr', 'en']);

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertSame(['en', 'hr', 'de'], $block->getAvailableLocales());
        $this->assertSame('hr', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocalesAndAlwaysAvailable(): void
    {
        $persistenceBlock = new Block(
            [
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock, ['fr', 'no']);

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertSame(['en', 'hr', 'de'], $block->getAvailableLocales());
        $this->assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "42"
     */
    public function testMapBlockWithLocalesAndAlwaysAvailableWithoutUsingMainLocale(): void
    {
        $persistenceBlock = new Block(
            [
                'id' => 42,
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $this->blockMapper->mapBlock($persistenceBlock, ['fr', 'no'], false);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "42"
     */
    public function testMapBlockWithLocalesAndNotAlwaysAvailable(): void
    {
        $persistenceBlock = new Block(
            [
                'id' => 42,
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en', 'hr', 'de'],
                'parameters' => ['en' => [], 'hr' => [], 'de' => []],
                'config' => [],
            ]
        );

        $this->blockMapper->mapBlock($persistenceBlock, ['fr', 'no']);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     */
    public function testMapBlockWithInvalidDefinition(): void
    {
        $persistenceBlock = new Block(
            [
                'id' => 31,
                'layoutId' => 13,
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
                    'http_cache' => [
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ],
                ],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertInstanceOf(NullBlockDefinition::class, $block->getDefinition());

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertSame(31, $block->getId());
        $this->assertSame(13, $block->getLayoutId());
        $this->assertSame('default', $block->getViewType());
        $this->assertSame('standard', $block->getItemViewType());
        $this->assertSame('My block', $block->getName());
        $this->assertSame(3, $block->getParentPosition());
        $this->assertTrue($block->isPublished());

        $this->assertFalse($block->hasParameter('css_class'));
        $this->assertFalse($block->hasParameter('css_id'));

        $this->assertFalse($block->hasConfig('http_cache'));

        $this->assertTrue($block->isTranslatable());
        $this->assertSame('en', $block->getMainLocale());
        $this->assertFalse($block->isAlwaysAvailable());
        $this->assertSame(['en'], $block->getAvailableLocales());

        $this->assertSame('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapContainerBlock(): void
    {
        $persistenceBlock = new Block(
            [
                'id' => 33,
                'definitionIdentifier' => 'two_columns',
                'status' => Value::STATUS_PUBLISHED,
                'name' => 'My block',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => ['en'],
                'parameters' => ['en' => []],
                'config' => [
                    'http_cache' => [
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ],
                ],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('two_columns'),
            $block->getDefinition()
        );

        $this->assertTrue($block->hasPlaceholder('left'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('left'));

        $placeholder = $block->getPlaceholder('left');
        $this->assertSame('left', $placeholder->getIdentifier());
        $this->assertCount(1, $placeholder->getBlocks());
        $this->assertInstanceOf(APIBlock::class, $placeholder->getBlocks()[0]);

        $this->assertTrue($block->hasPlaceholder('right'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('right'));

        $placeholder = $block->getPlaceholder('right');
        $this->assertSame('right', $placeholder->getIdentifier());
        $this->assertCount(0, $placeholder->getBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapCollectionReferences
     */
    public function testMapBlockWithCollectionReferences(): void
    {
        $persistenceBlock = new Block(
            [
                'id' => 31,
                'layoutId' => 13,
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
                    'http_cache' => [
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ],
                ],
            ]
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertSame(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition()
        );

        $this->assertTrue($block->hasCollection('default'));
        $this->assertInstanceOf(Collection::class, $block->getCollection('default'));

        $this->assertTrue($block->hasCollection('featured'));
        $this->assertInstanceOf(Collection::class, $block->getCollection('featured'));
    }
}
