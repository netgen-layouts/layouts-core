<?php

namespace Netgen\BlockManager\Registry\ViewTemplateProviderRegistry;

use Netgen\BlockManager\Registry\ViewTemplateProviderRegistry;
use Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider;
use Netgen\BlockManager\View\ViewInterface;
use InvalidArgumentException;

class ArrayBased implements ViewTemplateProviderRegistry
{
    /**
     * @var \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider[]
     */
    protected $viewTemplateProviders = array();

    /**
     * Adds a view template provider to registry.
     *
     * @param \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider $viewTemplateProvider
     * @param string $type
     */
    public function addViewTemplateProvider(ViewTemplateProvider $viewTemplateProvider, $type)
    {
        $this->viewTemplateProviders[$type] = $viewTemplateProvider;
    }

    /**
     * Returns a view template provider for specified view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider
     */
    public function getViewTemplateProvider(ViewInterface $view)
    {
        $type = get_class($view);
        if (isset($this->viewTemplateProviders[$type])) {
            return $this->viewTemplateProviders[$type];
        }

        throw new InvalidArgumentException('View template provider for "' . $type . '" view does not exist.');
    }

    /**
     * Returns all view template providers.
     *
     * @return \Netgen\BlockManager\View\TemplateProvider\ViewTemplateProvider[]
     */
    public function getViewTemplateProviders()
    {
        return $this->viewTemplateProviders;
    }
}
