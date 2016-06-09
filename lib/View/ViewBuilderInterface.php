<?php

namespace Netgen\BlockManager\View;

interface ViewBuilderInterface
{
    /**
     * Builds the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($valueObject, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW);
}
