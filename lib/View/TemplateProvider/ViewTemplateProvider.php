<?php

namespace Netgen\BlockManager\View\TemplateProvider;

use Netgen\BlockManager\View\ViewInterface;

interface ViewTemplateProvider
{
    /**
     * Provides a template for the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function provideTemplate(ViewInterface $view);
}
