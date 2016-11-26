<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Parameters\ParameterValue;
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
        return new ParameterView(
            array(
                'valueObject' => $valueObject,
                'parameters' => array(
                    'parameter' => $valueObject,
                ),
            )
        );
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
        return $valueObject instanceof ParameterValue;
    }
}
