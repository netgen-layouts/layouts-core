<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlockCollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
    public function __construct(CollectionService $collectionService, BlockService $blockService)
    {
        $this->collectionService = $collectionService;
        $this->blockService = $blockService;
    }

    /**
     * Loads all block collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollections(Block $block)
    {
        $collections = array_map(
            function (Collection $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->loadBlockCollections($block, array(Collection::TYPE_MANUAL, Collection::TYPE_DYNAMIC))
        );

        return new ValueArray($collections);
    }

    /**
     * Loads all named collections attached to block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadNamedCollections(Block $block)
    {
        $collections = array_map(
            function (Collection $collection) {
                return new VersionedValue($collection, Version::API_V1);
            },
            $this->loadBlockCollections($block, array(Collection::TYPE_NAMED))
        );

        return new ValueArray($collections);
    }

    /**
     * Updates the block to change the collection type it has.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function changeCollectionType(Block $block, Request $request)
    {
    }

    /**
     * Loads all block collections.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $types
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Collection[]
     */
    protected function loadBlockCollections(Block $block, array $types = array())
    {
        $collections = array();
        $collectionReferences = $this->blockService->loadCollectionReferences($block);

        foreach ($collectionReferences as $collectionReference) {
            $collection = $this->collectionService->loadCollection(
                $collectionReference->getCollectionId(),
                $collectionReference->getCollectionStatus()
            );

            if (in_array($collection->getType(), $types)) {
                $collections[] = $collection;
            }
        }

        return $collections;
    }
}
