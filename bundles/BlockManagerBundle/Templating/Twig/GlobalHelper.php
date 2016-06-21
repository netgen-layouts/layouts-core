<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\View\LayoutViewInterface;

class GlobalHelper
{
    /**
     * @var \Netgen\BlockManager\View\LayoutViewInterface
     */
    protected $layoutView;

    /**
     * @var string
     */
    protected $pageLayout;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the layout view object.
     *
     * @return \Netgen\BlockManager\View\LayoutViewInterface
     */
    public function getLayoutView()
    {
        return $this->layoutView;
    }

    /**
     * Sets the layout view object.
     *
     * @param \Netgen\BlockManager\View\LayoutViewInterface $layoutView
     */
    public function setLayoutView(LayoutViewInterface $layoutView)
    {
        $this->layoutView = $layoutView;
    }

    /**
     * Sets the pagelayout template.
     *
     * @param string
     */
    public function setPageLayout($pageLayout)
    {
        $this->pageLayout = $pageLayout;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayout()
    {
        return $this->pageLayout;
    }

    /**
     * Returns the configuration object.
     *
     * @return \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    public function getConfig()
    {
        return $this->configuration;
    }
}
