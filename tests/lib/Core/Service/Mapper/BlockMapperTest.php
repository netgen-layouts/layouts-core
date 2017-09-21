<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\API\Values\Block\CollectionReference;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class BlockMapperTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->blockMapper = $this->createBlockMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlock()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 31,
                'layoutId' => 13,
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => array(
                    'en' => array(
                        'css_class' => 'test',
                        'some_param' => 'some_value',
                    ),
                ),
                'config' => array(
                    'http_cache' => array(
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ),
                ),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertEquals(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition()
        );

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertEquals(31, $block->getId());
        $this->assertEquals(13, $block->getLayoutId());
        $this->assertEquals('default', $block->getViewType());
        $this->assertEquals('standard', $block->getItemViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(Value::STATUS_PUBLISHED, $block->getStatus());
        $this->assertTrue($block->isPublished());

        $this->assertEquals('test', $block->getParameter('css_class')->getValue());
        $this->assertNull($block->getParameter('css_id')->getValue());

        $this->assertTrue($block->hasConfig('http_cache'));
        $this->assertInstanceOf(Config::class, $block->getConfig('http_cache'));

        $httpCacheConfig = $block->getConfig('http_cache');

        $this->assertTrue($httpCacheConfig->getParameter('use_http_cache')->getValue());
        $this->assertEquals(400, $httpCacheConfig->getParameter('shared_max_age')->getValue());

        $this->assertTrue($block->isTranslatable());
        $this->assertEquals('en', $block->getMainLocale());
        $this->assertFalse($block->isAlwaysAvailable());
        $this->assertEquals(array('en'), $block->getAvailableLocales());

        $this->assertEquals('en', $block->getLocale());

        $this->assertEquals('test', $block->getParameter('css_class')->getValue());
        $this->assertNull($block->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocale()
    {
        $persistenceBlock = new Block(
            array(
                'definitionIdentifier' => 'text',
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
                'config' => array(),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock, 'hr');

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertEquals(array('en', 'hr', 'de'), $block->getAvailableLocales());
        $this->assertEquals('hr', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocales()
    {
        $persistenceBlock = new Block(
            array(
                'definitionIdentifier' => 'text',
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
                'config' => array(),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock, array('hr', 'en'));

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertEquals(array('en', 'hr', 'de'), $block->getAvailableLocales());
        $this->assertEquals('hr', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapBlockWithLocalesAndAlwaysAvailable()
    {
        $persistenceBlock = new Block(
            array(
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
                'config' => array(),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock, array('fr', 'no'));

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertEquals(array('en', 'hr', 'de'), $block->getAvailableLocales());
        $this->assertEquals('en', $block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "42"
     */
    public function testMapBlockWithLocalesAndAlwaysAvailableWithoutUsingMainLocale()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 42,
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
                'config' => array(),
            )
        );

        $this->blockMapper->mapBlock($persistenceBlock, array('fr', 'no'), false);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     * @expectedExceptionMessage Could not find block with identifier "42"
     */
    public function testMapBlockWithLocalesAndNotAlwaysAvailable()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 42,
                'definitionIdentifier' => 'text',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => array('en', 'hr', 'de'),
                'parameters' => array('en' => array(), 'hr' => array(), 'de' => array()),
                'config' => array(),
            )
        );

        $this->blockMapper->mapBlock($persistenceBlock, array('fr', 'no'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapContainerBlock()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 33,
                'definitionIdentifier' => 'two_columns',
                'status' => Value::STATUS_PUBLISHED,
                'name' => 'My block',
                'alwaysAvailable' => false,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'parameters' => array('en' => array()),
                'config' => array(
                    'http_cache' => array(
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ),
                ),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertEquals(
            $this->blockDefinitionRegistry->getBlockDefinition('two_columns'),
            $block->getDefinition()
        );

        $this->assertTrue($block->hasPlaceholder('left'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('left'));

        $placeholder = $block->getPlaceholder('left');
        $this->assertEquals('left', $placeholder->getIdentifier());
        $this->assertCount(1, $placeholder->getBlocks());
        $this->assertInstanceOf(APIBlock::class, $placeholder->getBlocks()[0]);

        $this->assertTrue($block->hasPlaceholder('right'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('right'));

        $placeholder = $block->getPlaceholder('right');
        $this->assertEquals('right', $placeholder->getIdentifier());
        $this->assertCount(0, $placeholder->getBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapCollectionReferences
     */
    public function testMapBlockWithCollectionReferences()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 31,
                'layoutId' => 13,
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'alwaysAvailable' => false,
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'availableLocales' => array('en'),
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => array(
                    'en' => array(
                        'css_class' => 'test',
                        'some_param' => 'some_value',
                    ),
                ),
                'config' => array(
                    'http_cache' => array(
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ),
                ),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertEquals(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getDefinition()
        );

        $this->assertTrue($block->hasCollectionReference('default'));
        $this->assertInstanceOf(CollectionReference::class, $block->getCollectionReference('default'));

        $collectionReference = $block->getCollectionReference('default');
        $this->assertEquals('default', $collectionReference->getIdentifier());
        $this->assertInstanceOf(Collection::class, $collectionReference->getCollection());

        $this->assertTrue($block->hasCollectionReference('featured'));
        $this->assertInstanceOf(CollectionReference::class, $block->getCollectionReference('featured'));

        $collectionReference = $block->getCollectionReference('featured');
        $this->assertEquals('featured', $collectionReference->getIdentifier());
        $this->assertInstanceOf(Collection::class, $collectionReference->getCollection());
    }
}
