<?php

namespace Netgen\BlockManager\View;

class SerializableView
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    protected $view;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     */
    public function __construct(ViewInterface $view)
    {
        $this->view = $view;
    }

    /**
     * Returns the view
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Returns the API version
     *
     * @return int
     */
    public function getVersion()
    {
        if ($this->view->hasParameter('api_version')) {
            return $this->view->getParameter('api_version');
        }

        return 1;
    }
}
