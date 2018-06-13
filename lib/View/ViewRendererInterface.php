<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

interface ViewRendererInterface
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
