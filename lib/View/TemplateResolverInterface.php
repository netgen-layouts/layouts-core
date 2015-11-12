<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\View\ViewInterface;

interface TemplateResolverInterface
{
    /**
     * Resolves a view template.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function resolveTemplate(ViewInterface $view);
}
