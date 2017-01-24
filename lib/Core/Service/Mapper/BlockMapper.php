<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Block\PlaceholderDefinitionInterface;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\CollectionReference;
use Netgen\BlockManager\Core\Values\Page\Placeholder;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Persistence\Values\Page\Block as PersistenceBlock;
use Netgen\BlockManager\Persistence\Values\Page\CollectionReference as PersistenceCollectionReference;

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
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function mapBlock(PersistenceBlock $block)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $block->definitionIdentifier
        );

        $placeholders = array();
        if ($blockDefinition->isContainer()) {
            foreach ($blockDefinition->getPlaceholders() as $identifier => $placeholderDefinition) {
                $placeholders[$identifier] = $this->mapPlaceholder(
                    $placeholderDefinition,
                    // @todo Map the placeholder blocks
                    array(),
                    isset($block->placeholderParameters[$identifier]) ?
                        $block->placeholderParameters[$identifier] :
                        array()
                );
            }
        }

        $blockData = array(
            'id' => $block->id,
            'definition' => $blockDefinition,
            'viewType' => $block->viewType,
            'itemViewType' => $block->itemViewType,
            'name' => $block->name,
            'status' => $block->status,
            'published' => $block->status === Value::STATUS_PUBLISHED,
            'placeholders' => $placeholders,
            'parameters' => $this->parameterMapper->mapParameters(
                $blockDefinition,
                $block->parameters
            ),
        );

        return new Block($blockData);
    }

    /**
     * Maps the placeholder from persistence parameters.
     *
     * @param \Netgen\BlockManager\Block\PlaceholderDefinitionInterface $placeholderDefinition
     * @param \Netgen\BlockManager\API\Values\Page\Block[] $blocks
     * @param array $placeholderParameters
     *
     * @return \Netgen\BlockManager\Core\Values\Page\Placeholder
     */
    public function mapPlaceholder(
        PlaceholderDefinitionInterface $placeholderDefinition,
        array $blocks,
        array $placeholderParameters
    ) {
        return new Placeholder(
            array(
                'identifier' => $placeholderDefinition->getIdentifier(),
                'blocks' => $blocks,
                'parameters' => $this->parameterMapper->mapParameters(
                    $placeholderDefinition,
                    $placeholderParameters
                ),
            )
        );
    }

    /**
     * Builds the API collection reference value object from persistence one.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Page\Block $block
     * @param \Netgen\BlockManager\Persistence\Values\Page\CollectionReference $collectionReference
     *
     * @return \Netgen\BlockManager\API\Values\Page\CollectionReference
     */
    public function mapCollectionReference(PersistenceBlock $block, PersistenceCollectionReference $collectionReference)
    {
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
