<?php

namespace Netgen\BlockManager\Registry;

use Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider;
use Netgen\BlockManager\View\ViewInterface;

interface ViewTemplateProviderRegistry
{
    /**
     * Adds a view template provider to registry.
     *
     * @param \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider $viewTemplateProvider
     * @param string $type
     */
    public function addViewTemplateProvider(ViewTemplateProvider $viewTemplateProvider, $type);

    /**
     * Returns a view template provider for specified view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider
     */
    public function getViewTemplateProvider(ViewInterface $view);

    /**
     * Returns all view template providers.
     *
     * @return \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider[]
     */
    public function getViewTemplateProviders();
}
