<?php

namespace Netgen\BlockManager\Core;

use Netgen\BlockManager\API\Repository as APIRepository;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Persistence\Handler;
use Netgen\BlockManager\Exception\RuntimeException;
use Exception;

class Repository implements APIRepository
{
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    protected $layoutResolverService;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(
        LayoutService $layoutService,
        BlockService $blockService,
        CollectionService $collectionService,
        LayoutResolverService $layoutResolverService,
        Handler $persistenceHandler
    ) {
        $this->layoutService = $layoutService;
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->layoutResolverService = $layoutResolverService;
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * Returns the layout service.
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    public function getLayoutService()
    {
        return $this->layoutService;
    }

    /**
     * Returns the block service.
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    public function getBlockService()
    {
        return $this->blockService;
    }

    /**
     * Returns the collection service.
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    public function getCollectionService()
    {
        return $this->collectionService;
    }

    /**
     * Returns the layout resolver service.
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    public function getLayoutResolverService()
    {
        return $this->layoutResolverService;
    }

    /**
     * Begins a transaction.
     */
    public function beginTransaction()
    {
        $this->persistenceHandler->beginTransaction();
    }

    /**
     * Commits the transaction.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function commitTransaction()
    {
        try {
            $this->persistenceHandler->commitTransaction();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Rollbacks the transaction.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function rollbackTransaction()
    {
        try {
            $this->persistenceHandler->rollbackTransaction();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}
