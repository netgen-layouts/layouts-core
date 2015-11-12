<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\View\LayoutViewInterface;

class GlobalHelper
{
    /**
     * @var string
     */
    protected $pagelayout;

    /**
     * @var \Netgen\BlockManager\View\LayoutViewInterface
     */
    protected $layoutView;

    /**
     * Returns the pagelayout.
     *
     * @return string
     */
    public function getPagelayout()
    {
        return $this->pagelayout;
    }

    /**
     * Sets the pagelayout.
     *
     * @param string $pagelayout
     */
    public function setPagelayout($pagelayout)
    {
        $this->pagelayout = $pagelayout;
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
