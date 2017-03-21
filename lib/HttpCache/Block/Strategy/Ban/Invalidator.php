<?php

namespace Netgen\BlockManager\HttpCache\Block\Strategy\Ban;

use FOS\HttpCacheBundle\CacheManager;
use Netgen\BlockManager\HttpCache\Block\InvalidatorInterface;

class Invalidator implements InvalidatorInterface
{
    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    protected $cacheManager;

    /**
     * Constructor.
     *
     * @param \FOS\HttpCacheBundle\CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidate(array $blockIds)
    {
        if (empty($blockIds)) {
            return;
        }

        $this->cacheManager->invalidate(
            array(
                'X-Block-Id' => '^(' . implode('|', $blockIds) . ')$',
            )
        );
    }

    /**
     * Invalidates all blocks in provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayoutBlocks(array $layoutIds)
    {
        if (empty($layoutIds)) {
            return;
        }

        $this->cacheManager->invalidate(
            array(
                'X-Origin-Layout-Id' => '^(' . implode('|', $layoutIds) . ')$',
            )
        );
    }

    /**
     * Invalidates all blocks.
     */
    public function invalidateAll()
    {
        $this->cacheManager->invalidate(
            array(
                'X-Block-Id' => '.*',
            )
        );
    }
}
