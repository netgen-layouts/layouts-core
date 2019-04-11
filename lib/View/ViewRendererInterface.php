<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

interface ViewRendererInterface
{
    /**
     * Renders the view.
     */
    public function renderView(ViewInterface $view): string;
}
