<?php

namespace Netgen\BlockManager\View\Renderer;

use Netgen\BlockManager\View\ViewInterface;

interface ViewRenderer
{
    /**
     * Renders the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function renderView(ViewInterface $view);
}
