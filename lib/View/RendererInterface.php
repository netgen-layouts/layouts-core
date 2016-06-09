<?php

namespace Netgen\BlockManager\View;

interface RendererInterface
{
    /**
     * Renders the value object.
     *
     * @param mixed $valueObject
     * @param array $parameters
     * @param string $context
     *
     * @return string
     */
    public function renderValueObject($valueObject, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW);

    /**
     * Renders the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return string
     */
    public function renderView(ViewInterface $view);
}
