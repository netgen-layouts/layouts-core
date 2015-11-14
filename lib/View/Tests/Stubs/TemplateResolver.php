<?php

namespace Netgen\BlockManager\View\Tests\Stubs;

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
