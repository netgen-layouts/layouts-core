<?php

namespace Netgen\BlockManager\HttpCache\Layout\Strategy\Ban;

use FOS\HttpCache\CacheInvalidator;
use Netgen\BlockManager\HttpCache\Layout\InvalidatorInterface;

class Invalidator implements InvalidatorInterface
{
    /**
     * @var \FOS\HttpCache\CacheInvalidator
     */
    protected $fosInvalidator;

    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProviderInterface
     */
    protected $layoutIdProvider;

    /**
     * Constructor.
     *
     * @param \FOS\HttpCache\CacheInvalidator $fosInvalidator
     * @param \Netgen\BlockManager\HttpCache\Layout\Strategy\Ban\IdProviderInterface $layoutIdProvider
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

        $this->fosInvalidator->invalidate(
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
        $this->fosInvalidator->invalidate(
            array(
                'X-Layout-Id' => '.*',
            )
        );
    }
}
