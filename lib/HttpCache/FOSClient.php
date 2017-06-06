<?php

namespace Netgen\BlockManager\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\BlockManager\HttpCache\Layout\IdProviderInterface;

class FOSClient implements ClientInterface
{
    /**
     * @var \FOS\HttpCache\CacheInvalidator
     */
    protected $fosInvalidator;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\IdProviderInterface
     */
    protected $layoutIdProvider;

    /**
     * Constructor.
     *
     * @param \FOS\HttpCache\CacheInvalidator $fosInvalidator
     * @param \Netgen\BlockManager\HttpCache\Layout\IdProviderInterface $layoutIdProvider
     */
    public function __construct(CacheInvalidator $fosInvalidator, IdProviderInterface $layoutIdProvider)
    {
        $this->fosInvalidator = $fosInvalidator;
        $this->layoutIdProvider = $layoutIdProvider;
    }

    /**
     * Invalidates all provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayouts(array $layoutIds)
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

        $this->fosInvalidator->invalidate(
            array(
                'X-Layout-Id' => '^(' . implode('|', $allLayoutIds) . ')$',
            )
        );
    }

    /**
     * Invalidates all layouts.
     */
    public function invalidateAllLayouts()
    {
        $this->fosInvalidator->invalidate(
            array(
                'X-Layout-Id' => '.*',
            )
        );
    }

    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidateBlocks(array $blockIds)
    {
        if (empty($blockIds)) {
            return;
        }

        $this->fosInvalidator->invalidate(
            array(
                'X-Block-Id' => '^(' . implode('|', $blockIds) . ')$',
            )
        );
    }

    /**
     * Invalidates all blocks from provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayoutBlocks(array $layoutIds)
    {
        if (empty($layoutIds)) {
            return;
        }

        $this->fosInvalidator->invalidate(
            array(
                'X-Origin-Layout-Id' => '^(' . implode('|', $layoutIds) . ')$',
            )
        );
    }

    /**
     * Invalidates all blocks.
     */
    public function invalidateAllBlocks()
    {
        $this->fosInvalidator->invalidate(
            array(
                'X-Block-Id' => '.*',
            )
        );
    }

    /**
     * Commits the cache clear operations to the backend.
     *
     * @return bool
     */
    public function commit()
    {
        try {
            $this->fosInvalidator->flush();
        } catch (ExceptionCollection $e) {
            // Do nothing, FOS invalidator will write to log.
            return false;
        }

        return true;
    }
}
