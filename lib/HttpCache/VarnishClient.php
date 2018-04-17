<?php

namespace Netgen\BlockManager\HttpCache;

use FOS\HttpCache\CacheInvalidator;
use FOS\HttpCache\Exception\ExceptionCollection;
use Netgen\BlockManager\HttpCache\Layout\IdProviderInterface;

final class VarnishClient implements ClientInterface
{
    /**
     * @var \FOS\HttpCache\CacheInvalidator
     */
    private $fosInvalidator;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\IdProviderInterface
     */
    private $layoutIdProvider;

    public function __construct(CacheInvalidator $fosInvalidator, IdProviderInterface $layoutIdProvider)
    {
        $this->fosInvalidator = $fosInvalidator;
        $this->layoutIdProvider = $layoutIdProvider;
    }

    public function invalidateLayouts(array $layoutIds)
    {
        if (empty($layoutIds)) {
            return;
        }

        $allLayoutIds = [];
        foreach ($layoutIds as $layoutId) {
            $allLayoutIds = array_merge(
                $allLayoutIds,
                $this->layoutIdProvider->provideIds($layoutId)
            );
        }

        $this->fosInvalidator->invalidate(
            [
                'X-Layout-Id' => '^(' . implode('|', $allLayoutIds) . ')$',
            ]
        );
    }

    public function invalidateAllLayouts()
    {
        $this->fosInvalidator->invalidate(
            [
                'X-Layout-Id' => '.*',
            ]
        );
    }

    public function invalidateBlocks(array $blockIds)
    {
        if (empty($blockIds)) {
            return;
        }

        $this->fosInvalidator->invalidate(
            [
                'X-Block-Id' => '^(' . implode('|', $blockIds) . ')$',
            ]
        );
    }

    public function invalidateLayoutBlocks(array $layoutIds)
    {
        if (empty($layoutIds)) {
            return;
        }

        $this->fosInvalidator->invalidate(
            [
                'X-Origin-Layout-Id' => '^(' . implode('|', $layoutIds) . ')$',
            ]
        );
    }

    public function invalidateAllBlocks()
    {
        $this->fosInvalidator->invalidate(
            [
                'X-Block-Id' => '.*',
            ]
        );
    }

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
