<?php

namespace Netgen\BlockManager\HttpCache;

use Netgen\BlockManager\HttpCache\Block\InvalidatorInterface as BlockInvalidatorInterface;
use Netgen\BlockManager\HttpCache\Layout\InvalidatorInterface as LayoutInvalidatorInterface;

class Client implements ClientInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Layout\InvalidatorInterface
     */
    protected $layoutInvalidator;

    /**
     * @var \Netgen\BlockManager\HttpCache\Block\InvalidatorInterface
     */
    protected $blockInvalidator;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\HttpCache\Layout\InvalidatorInterface $layoutInvalidator
     * @param \Netgen\BlockManager\HttpCache\Block\InvalidatorInterface $blockInvalidator
     */
    public function __construct(
        LayoutInvalidatorInterface $layoutInvalidator,
        BlockInvalidatorInterface $blockInvalidator
    ) {
        $this->layoutInvalidator = $layoutInvalidator;
        $this->blockInvalidator = $blockInvalidator;
    }

    /**
     * Invalidates all provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayouts(array $layoutIds)
    {
        $this->layoutInvalidator->invalidate($layoutIds);
    }

    /**
     * Invalidates all layouts.
     */
    public function invalidateAllLayouts()
    {
        $this->layoutInvalidator->invalidateAll();
    }

    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidateBlocks(array $blockIds)
    {
        $this->blockInvalidator->invalidate($blockIds);
    }

    /**
     * Invalidates all blocks.
     */
    public function invalidateAllBlocks()
    {
        $this->blockInvalidator->invalidateAll();
    }
}
