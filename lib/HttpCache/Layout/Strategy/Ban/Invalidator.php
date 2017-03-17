<?php

namespace Netgen\BlockManager\HttpCache\Layout\Strategy\Ban;

use FOS\HttpCacheBundle\CacheManager;
use Netgen\BlockManager\HttpCache\Layout\InvalidatorInterface;

class Invalidator implements InvalidatorInterface
{
    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    protected $cacheManager;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProviderInterface
     */
    protected $layoutIdProvider;

    /**
     * Constructor.
     *
     * @param \FOS\HttpCacheBundle\CacheManager $cacheManager
     * @param \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProviderInterface $layoutIdProvider
     */
    public function __construct(CacheManager $cacheManager, IdProviderInterface $layoutIdProvider)
    {
        $this->cacheManager = $cacheManager;
        $this->layoutIdProvider = $layoutIdProvider;
    }

    /**
     * Invalidates all provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidate(array $layoutIds)
    {
        if (empty($layoutIds)) {
            return;
        }

        $allLayoutIds = array();
        foreach ($layoutIds as $layoutId) {
            $allLayoutIds = array_merge(
                $allLayoutIds,
                $this->layoutIdProvider->provideIds($layoutId)
            );
        }

        $this->cacheManager->invalidate(
            array(
                'X-Layout-Id' => '^(' . implode('|', $allLayoutIds) . ')$',
            )
        );
    }

    /**
     * Invalidates all layouts.
     */
    public function invalidateAll()
    {
        $this->cacheManager->invalidate(
            array(
                'X-Layout-Id' => '.*',
            )
        );
    }
}
