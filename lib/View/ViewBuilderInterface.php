<?php

namespace Netgen\BlockManager\View;

interface ViewBuilderInterface
{
    /**
     * Builds the view.
     *
     * @param mixed $value
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($value, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array());
}
