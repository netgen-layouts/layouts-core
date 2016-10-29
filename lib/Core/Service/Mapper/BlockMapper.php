<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
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
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     * @param \Netgen\BlockManager\Core\Service\Mapper\CollectionMapper $collectionMapper
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(
        Handler $persistenceHandler,
        CollectionMapper $collectionMapper,
        ParameterMapper $parameterMapper,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    ) {
        parent::__construct($persistenceHandler);

        $this->collectionMapper = $collectionMapper;
        $this->parameterMapper = $parameterMapper;
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Builds the API block value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block|\Netgen\BlockManager\API\Values\Page\BlockDraft
     */
    public function mapBlock(PersistenceBlock $block)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $block->definitionIdentifier
        );

        $blockData = array(
            'id' => $block->id,
            'layoutId' => $block->layoutId,
            'zoneIdentifier' => $block->zoneIdentifier,
            'position' => $block->position,
            'blockDefinition' => $blockDefinition,
            'parameters' => $this->parameterMapper->mapParameters(
                $blockDefinition->getParameters(),
                $block->parameters
            ),
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
