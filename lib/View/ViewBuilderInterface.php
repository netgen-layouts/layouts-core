<?php

namespace Netgen\BlockManager\View;

interface ViewBuilderInterface
{
    /**
     * Builds the view.
     *
     * @param mixed $valueObject
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($valueObject, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = array());
}
