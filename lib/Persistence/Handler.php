<?php

namespace Netgen\BlockManager\Persistence;

interface Handler
{
    /**
     * Returns the block handler
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Block
     */
    public function getBlockHandler();

    /**
     * Returns the layout handler
     *
     * @return \Netgen\BlockManager\Persistence\Handler\Layout
     */
    public function getLayoutHandler();
}
