<?php

namespace Netgen\Bundle\BlockManagerAdminUIBundle\Controller\App;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\BlockDraft;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;

class BlockController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Displays block edit interface.
     *
     * @param \Netgen\BlockManager\API\Values\Page\BlockDraft $block
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(BlockDraft $block)
    {
        $defaultCollection = null;
        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            if ($collectionReference->getIdentifier() === 'default') {
                if ($collectionReference->getCollectionStatus() === Collection::STATUS_PUBLISHED) {
                    $defaultCollection = $this->collectionService->loadCollection(
                        $collectionReference->getCollectionId()
                    );
                } else {
                    $defaultCollection = $this->collectionService->loadCollectionDraft(
                        $collectionReference->getCollectionId()
                    );
                }
            }
        }

        return $this->render(
            'NetgenBlockManagerAdminUIBundle:app/block:edit.html.twig',
            array(
                'block' => $block,
                'block_definition' => $this->getBlockDefinition($block->getDefinitionIdentifier()),
                'collection' => $defaultCollection,
                'named_collections' => $this->collectionService->loadNamedCollections(),
                'query_types' => $this->queryTypeRegistry->getQueryTypes(),
            )
        );
    }
}
