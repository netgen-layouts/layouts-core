<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

interface ViewRendererInterface
{
    /**
     * Renders the view.
     */
    public function renderView(ViewInterface $view): string;
}
