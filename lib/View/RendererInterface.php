<?php

namespace Netgen\BlockManager\View;

interface RendererInterface
{
    /**
     * Renders the value.
     *
     * @param mixed $value
     * @param string $context
     * @param array $parameters
     *
     * @return string
     */
    public function renderValue($value, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array());

    /**
     * Renders the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function renderView(ViewInterface $view);
}
