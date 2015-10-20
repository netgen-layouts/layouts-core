<?php

namespace Netgen\BlockManager\View\TemplateProvider;

use Netgen\BlockManager\View\ViewInterface;

interface ViewTemplateProvider
{
    /**
     * Provides a template to the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     */
    public function provideTemplate(ViewInterface $view);
}
