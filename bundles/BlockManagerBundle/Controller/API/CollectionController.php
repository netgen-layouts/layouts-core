<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator;

class CollectionController extends Controller
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
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\Validator\CollectionValidator $validator
     */
    public function __construct(
        CollectionService $collectionService,
        BlockService $blockService,
        ConfigurationInterface $configuration,
        CollectionValidator $validator
    ) {
        $this->collectionService = $collectionService;
        $this->blockService = $blockService;
        $this->configuration = $configuration;
        $this->validator = $validator;
    }

    /**
     * Loads the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function view(Collection $collection)
    {
        return new VersionedValue($collection, self::API_VERSION);
    }

    /**
     * Loads the named collections.
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadNamedCollections()
    {
        $namedCollections = array_map(
            function (Collection $collection) {
                return new VersionedValue($collection, self::API_VERSION);
            },
            $this->collectionService->loadNamedCollections()
        );

        return new ValueArray($namedCollections);
    }

    /**
     * Loads all collection items.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionItems(Collection $collection)
    {
        $manualItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, self::API_VERSION);
            },
            array_values($collection->getManualItems())
        );

        $overrideItems = array_map(
            function (Item $item) {
                return new VersionedValue($item, self::API_VERSION);
            },
            array_values($collection->getOverrideItems())
        );

        return new ValueArray(
            array(
                'manual_items' => $manualItems,
                'override_items' => $overrideItems,
            )
        );
    }

    /**
     * Loads all collection queries.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     *
     * @return \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    public function loadCollectionQueries(Collection $collection)
    {
        $queries = array_map(
            function (Query $query) {
                return new VersionedValue($query, self::API_VERSION);
            },
            $collection->getQueries()
        );

        return new ValueArray($queries);
    }
}
