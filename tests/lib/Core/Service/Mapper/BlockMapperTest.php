<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\CollectionReference as APICollectionReference;
use Netgen\BlockManager\API\Values\Page\Placeholder;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Persistence\Values\Page\Block;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference;
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
                'definitionIdentifier' => 'text',
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => array(
                    'css_class' => 'test',
                    'some_param' => 'some_value',
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
        $this->assertEquals('default', $block->getViewType());
        $this->assertEquals('standard', $block->getItemViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(Value::STATUS_PUBLISHED, $block->getStatus());
        $this->assertTrue($block->isPublished());

        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $block->getDefinition()->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $block->getDefinition()->getParameters()['css_id'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
            ),
            $block->getParameters()
        );
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
                'definitionIdentifier' => 'div_container',
                'status' => Value::STATUS_PUBLISHED,
                'parameters' => array(),
                'placeholderParameters' => array(
                    'main' => array(
                        'css_class' => 'test2',
                        'some_param' => 'some_value2',
                    ),
                ),
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertEquals(
            $this->blockDefinitionRegistry->getBlockDefinition('div_container'),
            $block->getDefinition()
        );

        $this->assertTrue($block->hasPlaceholder('main'));
        $this->assertInstanceOf(Placeholder::class, $block->getPlaceholder('main'));

        $placeholder = $block->getPlaceholder('main');
        $placeholderDefinition = $block->getDefinition()->getPlaceholder('main');

        $this->assertEquals('main', $placeholder->getIdentifier());
        $this->assertCount(1, $placeholder->getBlocks());
        $this->assertInstanceOf(APIBlock::class, $placeholder->getBlocks()[0]);

        $this->assertEquals(
            array(
                'css_class' => new ParameterValue(
                    array(
                        'name' => 'css_class',
                        'parameter' => $placeholderDefinition->getParameters()['css_class'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'test2',
                        'isEmpty' => false,
                    )
                ),
                'css_id' => new ParameterValue(
                    array(
                        'name' => 'css_id',
                        'parameter' => $placeholderDefinition->getParameters()['css_id'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
            ),
            $placeholder->getParameters()
        );
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
