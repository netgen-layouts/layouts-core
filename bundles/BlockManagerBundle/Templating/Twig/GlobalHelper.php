<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

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
     * Returns the resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        return $this->layoutView ? $this->layoutView->getLayout() : null;
    }

    /**
     * Returns the resolved layout template.
     *
     * @return string
     */
    public function getLayoutTemplate()
    {
        return $this->layoutView ? $this->layoutView->getTemplate() : null;
    }
}
