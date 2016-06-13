<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Core\Values\Page\BlockDraft;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Page\Layout as PersistenceLayout;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference as PersistenceCollectionReference;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Persistence\Handler;

class BlockMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper
     */
    protected $collectionMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $collectionMapper
     */
    public function __construct(Handler $persistenceHandler, CollectionMapper $collectionMapper)
    {
        parent::__construct($persistenceHandler);

        $this->collectionMapper = $collectionMapper;
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function mapBlock(PersistenceBlock $block)
    {
        $blockData = array(
            'id' => $block->id,
            'layoutId' => $block->layoutId,
            'zoneIdentifier' => $block->zoneIdentifier,
            'position' => $block->position,
            'definitionIdentifier' => $block->definitionIdentifier,
            'parameters' => $block->parameters,
            'viewType' => $block->viewType,
            'itemViewType' => $block->itemViewType,
            'name' => $block->name,
            'status' => $block->status,
        );

        return $block->status === PersistenceLayout::STATUS_PUBLISHED ?
            new Block($blockData) :
            new BlockDraft($blockData);
    }

    /**
     * Builds the API collection reference value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\CollectionReference $collectionReference
     *
     * @return \Netgen\BlockManager\API\Values\Page\CollectionReference
     */
    public function mapCollectionReference(PersistenceCollectionReference $collectionReference)
    {
        $block = $this->persistenceHandler->getBlockHandler()->loadBlock(
            $collectionReference->blockId,
            $collectionReference->blockStatus
        );

        $collection = $this->persistenceHandler->getCollectionHandler()->loadCollection(
            $collectionReference->collectionId,
            $collectionReference->collectionStatus
        );

        return new CollectionReference(
            array(
                'block' => $this->mapBlock($block),
                'collection' => $this->collectionMapper->mapCollection($collection),
                'identifier' => $collectionReference->identifier,
                'offset' => $collectionReference->offset,
                'limit' => $collectionReference->limit,
            )
        );
    }
}
