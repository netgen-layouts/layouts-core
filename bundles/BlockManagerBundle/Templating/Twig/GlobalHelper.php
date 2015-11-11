<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\View\LayoutViewInterface;

class GlobalHelper
{
    /**
     * @var string
     */
    protected $defaultPagelayout;

    /**
     * @var \Netgen\BlockManager\View\LayoutViewInterface
     */
    protected $layoutView;

    /**
     * Returns the default pagelayout.
     *
     * @return string
     */
    public function getDefaultPagelayout()
    {
        return $this->defaultPagelayout;
    }

    /**
     * Sets the default pagelayout.
     *
     * @param string $defaultPagelayout
     */
    public function setDefaultPagelayout($defaultPagelayout)
    {
        $this->defaultPagelayout = $defaultPagelayout;
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
