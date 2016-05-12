<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Core\Values\Value;

interface ViewBuilderInterface
{
    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\Core\Values\Value $value
     * @param string $context
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, $context = ViewInterface::CONTEXT_VIEW, array $parameters = array());
}
