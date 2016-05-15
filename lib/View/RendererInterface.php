<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Value;

interface RendererInterface
{
    /**
     * Renders the value.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderValue(Value $value, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array());

    /**
     * Renders the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function renderView(ViewInterface $view);
}
