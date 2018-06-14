<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

interface TemplateResolverInterface
{
    /**
     * Resolves a view template from the matching configuration.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     */
    public function resolveTemplate(ViewInterface $view): void;
}
