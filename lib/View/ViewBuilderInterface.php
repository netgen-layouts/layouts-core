<?php

namespace Netgen\BlockManager\View;

interface ViewBuilderInterface
{
    /**
     * Builds the view from the provided value object in specified context.
     *
     * @param mixed $valueObject
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView($valueObject, $context = ViewInterface::CONTEXT_DEFAULT, array $parameters = array());
}
