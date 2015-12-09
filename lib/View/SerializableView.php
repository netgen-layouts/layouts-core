<?php

namespace Netgen\BlockManager\View;

class SerializableView
{
    /**
     * @var \Netgen\BlockManager\View\ViewInterface
     */
    public $view;

    /**
     * @var int
     */
    public $version;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param int $version
     */
    public function __construct(ViewInterface $view, $version)
    {
        $this->view = $view;
        $this->version = $version;
    }
}
