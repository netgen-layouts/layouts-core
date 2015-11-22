<?php

namespace Netgen\BlockManager\Tests\View\Stubs;

use Netgen\BlockManager\View\TemplateResolver\TemplateResolver as BaseTemplateResolver;
use Netgen\BlockManager\View\ViewInterface;

class TemplateResolver extends BaseTemplateResolver
{
    /**
     * Returns if this template resolver supports the provided view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function supports(ViewInterface $view)
    {
        return $view instanceof View;
    }
}
