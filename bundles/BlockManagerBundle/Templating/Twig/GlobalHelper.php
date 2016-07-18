<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;

class GlobalHelper
{
    /**
     * @var \Netgen\BlockManager\View\View\LayoutViewInterface
     */
    protected $layoutView;

    /**
     * @var string
     */
    protected $pageLayoutTemplate;

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
     * @return \Netgen\BlockManager\View\View\LayoutViewInterface
     */
    public function getLayoutView()
    {
        return $this->layoutView;
    }

    /**
     * Sets the layout view object.
     *
     * @param \Netgen\BlockManager\View\View\LayoutViewInterface $layoutView
     */
    public function setLayoutView(LayoutViewInterface $layoutView)
    {
        $this->layoutView = $layoutView;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        return $this->pageLayoutTemplate;
    }

    /**
     * Sets the pagelayout template.
     *
     * @param string
     */
    public function setPageLayoutTemplate($pageLayoutTemplate)
    {
        $this->pageLayoutTemplate = $pageLayoutTemplate;
    }

    /**
     * Returns the currently resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        if (!$this->layoutView instanceof LayoutViewInterface) {
            return;
        }

        return $this->layoutView->getLayout();
    }

    /**
     * Returns the currently valid layout template, or base pagelayout if
     * no layout was resolved.
     *
     * @return string
     */
    public function getLayoutTemplate()
    {
        if ($this->layoutView instanceof LayoutViewInterface) {
            return $this->layoutView->getTemplate();
        }

        return $this->pageLayoutTemplate;
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
