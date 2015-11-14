<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\View\LayoutViewInterface;

class GlobalHelper
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\BlockManager\View\LayoutViewInterface
     */
    protected $layoutView;

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
     * Returns the configuration object.
     *
     * @return \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
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
    public function setLayoutView(LayoutViewInterface $layoutView = null)
    {
        $this->layoutView = $layoutView;
    }
}
