<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\View\View\ParameterView;

class ParameterViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        /** @var \Netgen\BlockManager\Parameters\Parameter $valueObject */
        $parameterView = new ParameterView($valueObject);

        return $parameterView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof Parameter;
    }
}
