<?php

namespace Netgen\BlockManager\Core\Persistence\Doctrine;

use Netgen\BlockManager\Persistence\Handler as HandlerInterface;
use Netgen\BlockManager\Persistence\Handler\Block;
use Netgen\BlockManager\Persistence\Handler\Layout;

class Handler implements HandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Block
     */
    protected $blockHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Handler\Layout
     */
    protected $layoutHandler;

    /**
     * Returns the block handler
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Block
     */
    public function getBlockHandler()
    {
        return $this->blockHandler;
    }

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler\Block $blockHandler
     * @param \Netgen\BlockManager\Persistence\Handler\Layout $layoutHandler
     */
    public function __construct(Block $blockHandler, Layout $layoutHandler)
    {
        $this->blockHandler = $blockHandler;
        $this->layoutHandler = $layoutHandler;
    }

    /**
     * Returns the layout handler
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    public function getLayoutHandler()
    {
        return $this->layoutHandler;
    }
}
