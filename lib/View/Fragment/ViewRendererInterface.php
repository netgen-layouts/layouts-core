<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Fragment;

use Netgen\BlockManager\View\ViewInterface;

interface ViewRendererInterface
{
    /**
     * Returns if the view renderer supports the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function supportsView(ViewInterface $view);

    /**
     * Returns the controller that will be used to render the fragment.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return \Symfony\Component\HttpKernel\Controller\ControllerReference|null
     */
    public function getController(ViewInterface $view);
}
