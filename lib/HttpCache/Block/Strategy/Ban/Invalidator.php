<?php

namespace Netgen\BlockManager\HttpCache\Block\Strategy\Ban;

use FOS\HttpCache\CacheInvalidator;
use Netgen\BlockManager\HttpCache\Block\InvalidatorInterface;

class Invalidator implements InvalidatorInterface
{
    /**
     * @var \FOS\HttpCache\CacheInvalidator
     */
    protected $fosInvalidator;

    /**
     * Constructor.
     *
     * @param \FOS\HttpCache\CacheInvalidator $fosInvalidator
     */
    public function __construct(CacheInvalidator $fosInvalidator)
    {
        $this->fosInvalidator = $fosInvalidator;
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

        $this->fosInvalidator->invalidate(
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

        $this->fosInvalidator->invalidate(
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
        $this->fosInvalidator->invalidate(
            array(
                'X-Block-Id' => '.*',
            )
        );
    }
}
