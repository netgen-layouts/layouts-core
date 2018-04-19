<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollections
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParentPosition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isTranslatable
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        $this->assertNull($block->getId());
        $this->assertNull($block->getLayoutId());
        $this->assertNull($block->getDefinition());
        $this->assertEquals([], $block->getParameters());
        $this->assertEquals([], $block->getPlaceholders());
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertEquals([], $block->getCollections());
        $this->assertFalse($block->hasCollection('test'));
        $this->assertNull($block->getViewType());
        $this->assertNull($block->getItemViewType());
        $this->assertNull($block->getName());
        $this->assertNull($block->getParentPosition());
        $this->assertNull($block->getStatus());
        $this->assertNull($block->isTranslatable());
        $this->assertNull($block->getMainLocale());
        $this->assertNull($block->isAlwaysAvailable());
        $this->assertEquals([], $block->getAvailableLocales());
        $this->assertNull($block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollections
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParentPosition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasCollection
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isTranslatable
     */
    public function testSetProperties()
    {
        $block = new Block(
            [
                'id' => 42,
                'layoutId' => 24,
                'definition' => new BlockDefinition(),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentPosition' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'placeholders' => [
                    'main' => new Placeholder(['identifier' => 'main']),
                ],
                'collectionReferences' => [
                    'default' => new CollectionReference(['identifier' => 'default', 'collection' => new Collection(['id' => 42])]),
                ],
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => ['en'],
                'locale' => 'en',
                'parameters' => [
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ],
            ]
        );

        $this->assertEquals(42, $block->getId());
        $this->assertEquals(24, $block->getLayoutId());
        $this->assertEquals(new BlockDefinition(), $block->getDefinition());
        $this->assertEquals('some_value', $block->getParameter('some_param'));
        $this->assertFalse($block->hasParameter('test'));
        $this->assertTrue($block->hasParameter('some_param'));
        $this->assertEquals(new Placeholder(['identifier' => 'main']), $block->getPlaceholder('main'));
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertEquals(new Collection(['id' => 42]), $block->getCollection('default'));
        $this->assertFalse($block->hasCollection('test'));
        $this->assertTrue($block->hasCollection('default'));
        $this->assertEquals('default', $block->getViewType());
        $this->assertEquals('standard', $block->getItemViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(3, $block->getParentPosition());
        $this->assertEquals(Value::STATUS_PUBLISHED, $block->getStatus());
        $this->assertTrue($block->isPublished());
        $this->assertTrue($block->isTranslatable());
        $this->assertEquals('en', $block->getMainLocale());
        $this->assertTrue($block->isAlwaysAvailable());
        $this->assertEquals(['en'], $block->getAvailableLocales());
        $this->assertEquals('en', $block->getLocale());

        $this->assertEquals(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
            $block->getParameters()
        );

        try {
            $block->getParameter('test');
        } catch (ParameterException $e) {
            // Do nothing
        }

        $this->assertEquals(
            [
                'main' => new Placeholder(['identifier' => 'main']),
            ],
            $block->getPlaceholders()
        );

        try {
            $block->getPlaceholder('test');
        } catch (BlockException $e) {
            // Do nothing
        }

        $this->assertEquals(
            [
                'default' => new Collection(['id' => 42]),
            ],
            $block->getCollections()
        );

        try {
            $block->getCollection('test');
        } catch (BlockException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::buildDynamicParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasDynamicParameter
     */
    public function testGetDynamicParameter()
    {
        $block = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        $this->assertTrue($block->hasDynamicParameter('definition_param'));
        $this->assertEquals('definition_value', $block->getDynamicParameter('definition_param'));

        $this->assertTrue($block->hasDynamicParameter('closure_param'));
        $this->assertEquals('closure_value', $block->getDynamicParameter('closure_param'));

        $this->assertNull($block->getDynamicParameter('unknown_param'));
        $this->assertFalse($block->hasDynamicParameter('unknown_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isContextual
     */
    public function testIsContextual()
    {
        $query = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'handler' => new BlockDefinitionHandler(),
                    ]
                ),
            ]
        );

        $this->assertFalse($query->isContextual());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isCacheable
     */
    public function testIsCacheable()
    {
        $cacheableResolverMock = $this->createMock(CacheableResolverInterface::class);

        $block = new Block(
            [
                'definition' => new BlockDefinition(
                    [
                        'cacheableResolver' => $cacheableResolverMock,
                    ]
                ),
            ]
        );

        $cacheableResolverMock
            ->expects($this->any())
            ->method('isCacheable')
            ->with($this->equalTo($block))
            ->will($this->returnValue(false));

        $this->assertFalse($block->isCacheable());
    }
}
