<?php

namespace Netgen\BlockManager\View\TemplateResolver;

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

    /**
     * Returns if this template resolver supports the provided view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function supports(ViewInterface $view);
}
