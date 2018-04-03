<?php

namespace Netgen\BlockManager\Tests\Core\Values\Block;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Block\CollectionReference;
use Netgen\BlockManager\Core\Values\Block\Placeholder;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Exception\Core\BlockException;
use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use PHPUnit\Framework\TestCase;

final class BlockTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollectionReferences
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollectionReference
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasCollectionReference
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParentPosition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isTranslatable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLocale
     */
    public function testSetDefaultProperties()
    {
        $block = new Block();

        $this->assertNull($block->getId());
        $this->assertNull($block->getLayoutId());
        $this->assertNull($block->getDefinition());
        $this->assertEquals(array(), $block->getParameters());
        $this->assertEquals(array(), $block->getPlaceholders());
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertEquals(array(), $block->getCollections());
        $this->assertFalse($block->hasCollection('test'));
        $this->assertNull($block->getViewType());
        $this->assertNull($block->getItemViewType());
        $this->assertNull($block->getName());
        $this->assertNull($block->getParentPosition());
        $this->assertNull($block->getStatus());
        $this->assertNull($block->isTranslatable());
        $this->assertNull($block->getMainLocale());
        $this->assertNull($block->isAlwaysAvailable());
        $this->assertEquals(array(), $block->getAvailableLocales());
        $this->assertNull($block->getLocale());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::__construct
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDefinition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameters
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholders
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasPlaceholder
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollectionReferences
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getCollectionReference
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasCollectionReference
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getItemViewType
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getName
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getParentPosition
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isPublished
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isTranslatable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getMainLocale
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::isAlwaysAvailable
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getAvailableLocales
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getLocale
     */
    public function testSetProperties()
    {
        $block = new Block(
            array(
                'id' => 42,
                'layoutId' => 24,
                'definition' => new BlockDefinition('text'),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'parentPosition' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'placeholders' => array(
                    'main' => new Placeholder(array('identifier' => 'main')),
                ),
                'collectionReferences' => array(
                    'default' => new CollectionReference(array('identifier' => 'default', 'collection' => new Collection(array('id' => 42)))),
                ),
                'isTranslatable' => true,
                'mainLocale' => 'en',
                'alwaysAvailable' => true,
                'availableLocales' => array('en'),
                'locale' => 'en',
                'parameters' => array(
                    'some_param' => 'some_value',
                    'some_other_param' => 'some_other_value',
                ),
            )
        );

        $this->assertEquals(42, $block->getId());
        $this->assertEquals(24, $block->getLayoutId());
        $this->assertEquals(new BlockDefinition('text'), $block->getDefinition());
        $this->assertEquals('some_value', $block->getParameter('some_param'));
        $this->assertFalse($block->hasParameter('test'));
        $this->assertTrue($block->hasParameter('some_param'));
        $this->assertEquals(new Placeholder(array('identifier' => 'main')), $block->getPlaceholder('main'));
        $this->assertFalse($block->hasPlaceholder('test'));
        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertEquals(new Collection(array('id' => 42)), $block->getCollection('default'));
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
        $this->assertEquals(array('en'), $block->getAvailableLocales());
        $this->assertEquals('en', $block->getLocale());

        $this->assertEquals(
            array(
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ),
            $block->getParameters()
        );

        try {
            $block->getParameter('test');
        } catch (ParameterException $e) {
            // Do nothing
        }

        $this->assertEquals(
            array(
                'main' => new Placeholder(array('identifier' => 'main')),
            ),
            $block->getPlaceholders()
        );

        try {
            $block->getPlaceholder('test');
        } catch (BlockException $e) {
            // Do nothing
        }

        $this->assertEquals(
            array(
                'default' => new Collection(array('id' => 42)),
            ),
            $block->getCollections()
        );

        try {
            $block->getCollection('test');
        } catch (BlockException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::getDynamicParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::hasDynamicParameter
     * @covers \Netgen\BlockManager\Core\Values\Block\Block::buildDynamicParameters
     */
    public function testGetDynamicParameter()
    {
        $block = new Block(
            array(
                'definition' => new BlockDefinition('text'),
            )
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
            array(
                'definition' => new BlockDefinition('def'),
            )
        );

        $this->assertFalse($query->isContextual());
    }
}
