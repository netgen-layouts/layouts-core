<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Layout as APILayout;
use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
use Netgen\BlockManager\API\Values\Page\CollectionReference as APICollectionReference;
use Netgen\BlockManager\Parameters\ParameterVO;
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
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapBlock
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::__construct
     */
    public function testMapBlock()
    {
        $persistenceBlock = new Block(
            array(
                'id' => 1,
                'layoutId' => 1,
                'zoneIdentifier' => 'right',
                'position' => 3,
                'definitionIdentifier' => 'text',
                'parameters' => array(
                    'some_param' => 'some_value',
                ),
                'viewType' => 'default',
                'itemViewType' => 'standard',
                'name' => 'My block',
                'status' => APILayout::STATUS_PUBLISHED,
            )
        );

        $block = $this->blockMapper->mapBlock($persistenceBlock);

        $this->assertEquals(
            $this->blockDefinitionRegistry->getBlockDefinition('text'),
            $block->getBlockDefinition()
        );

        $this->assertInstanceOf(APIBlock::class, $block);
        $this->assertEquals(1, $block->getId());
        $this->assertEquals(1, $block->getLayoutId());
        $this->assertEquals('right', $block->getZoneIdentifier());
        $this->assertEquals(3, $block->getPosition());
        $this->assertEquals('default', $block->getViewType());
        $this->assertEquals('standard', $block->getItemViewType());
        $this->assertEquals('My block', $block->getName());
        $this->assertEquals(APILayout::STATUS_PUBLISHED, $block->getStatus());

        $this->assertEquals(
            array(
                'css_class' => new ParameterVO(
                    array(
                        'identifier' => 'css_class',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_class'],
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
                'css_id' => new ParameterVO(
                    array(
                        'identifier' => 'css_id',
                        'parameter' => $block->getBlockDefinition()->getParameters()['css_id'],
                        'value' => null,
                        'isEmpty' => true,
                    )
                ),
            ),
            $block->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\BlockMapper::mapCollectionReference
     */
    public function testMapCollectionReference()
    {
        $persistenceReference = new CollectionReference(
            array(
                'blockId' => 1,
                'blockStatus' => APILayout::STATUS_PUBLISHED,
                'collectionId' => 2,
                'collectionStatus' => Collection::STATUS_PUBLISHED,
                'identifier' => 'default',
                'offset' => 5,
                'limit' => 10,
            )
        );

        $reference = $this->blockMapper->mapCollectionReference($persistenceReference);

        $this->assertInstanceOf(APICollectionReference::class, $reference);

        $this->assertEquals(1, $reference->getBlock()->getId());
        $this->assertEquals(APILayout::STATUS_PUBLISHED, $reference->getBlock()->getStatus());
        $this->assertEquals(2, $reference->getCollection()->getId());
        $this->assertEquals(Collection::STATUS_PUBLISHED, $reference->getCollection()->getStatus());
        $this->assertEquals('default', $reference->getIdentifier());
        $this->assertEquals(5, $reference->getOffset());
        $this->assertEquals(10, $reference->getLimit());
    }
}
