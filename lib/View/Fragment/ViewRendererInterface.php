<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Fragment;

use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

interface ViewRendererInterface
{
    /**
     * Returns if the view renderer supports the view.
     */
    public function supportsView(ViewInterface $view): bool;

    /**
     * Returns the controller that will be used to render the fragment.
     */
    public function getController(ViewInterface $view): ?ControllerReference;
}
