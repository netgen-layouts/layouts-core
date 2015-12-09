<?php

namespace Netgen\BlockManager\View;

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
