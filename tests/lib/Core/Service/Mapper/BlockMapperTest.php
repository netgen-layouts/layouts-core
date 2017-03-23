<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\API\Values\Block\CollectionReference as APICollectionReference;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Persistence\Values\Block\Block;
use Netgen\BlockManager\Persistence\Values\Block\CollectionReference;
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
     * @covers \Netgen\BlockManager\Core\Service\Mapper\Mapper::__construct
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
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => array(
                    'css_class' => 'test',
                    'some_param' => 'some_value',
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
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\Mapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapPlaceholders
     */
    public function testMapContainerBlock()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 33,
                'definitionIdentifier' => 'column',
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => array(),
                'placeholderParameters' => array(
                    'main' => array(
                        'css_class' => 'test2',
                        'some_param' => 'some_value2',
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
            $this->blockDefinitionRegistry->getBlockDefinition('column'),
            $block->getDefinition()
        );

        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('main'));

        $placeholder = $block->getPlaceholder('main');
        $this->assertEquals('main', $placeholder->getIdentifier());
        $this->assertCount(1, $placeholder->getBlocks());
        $this->assertInstanceOf(APIBlock::class, $placeholder->getBlocks()[0]);

        $this->assertEquals('test2', $placeholder->getParameter('css_class')->getValue());
        $this->assertNull($placeholder->getParameter('css_id')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapCollectionReference
     */
    public function testMapCollectionReference()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 31,
                'definitionIdentifier' => 'text',
                'parameters' => array(
                    'some_param' => 'some_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Value::STATUS_PUBLISHED,
                'config' => array(
                    'http_cache' => array(
                        'use_http_cache' => true,
                        'shared_max_age' => 400,
                    ),
                ),
            )
        );

        $persistenceReference = new CollectionReference(
            array(
                'blockId' => 31,
                'blockStatus' => Value::STATUS_PUBLISHED,
                'collectionId' => 2,
                'collectionStatus' => Value::STATUS_PUBLISHED,
                'identifier' => 'default',
                'offset' => 5,
                'limit' => 10,
            )
        );

        $reference = $this->blockMapper->mapCollectionReference(
            $persistenceBlock,
            $persistenceReference
        );

        $this->assertInstanceOf(APICollectionReference::class, $reference);

        $this->assertEquals(31, $reference->getBlock()->getId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $reference->getBlock()->getStatus());
        $this->assertEquals(2, $reference->getCollection()->getId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $reference->getCollection()->getStatus());
        $this->assertEquals('default', $reference->getIdentifier());
        $this->assertEquals(5, $reference->getOffset());
        $this->assertEquals(10, $reference->getLimit());
    }
}
