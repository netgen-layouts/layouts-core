<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Value;

interface ViewBuilderInterface
{
    /**
     * Builds the view.
     *
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param array $parameters
     * @param string $context
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function buildView(Value $value, array $parameters = array(), $context = 'view');
}
