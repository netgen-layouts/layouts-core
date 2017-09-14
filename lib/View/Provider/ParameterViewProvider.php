<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\View\View\ParameterView;

class ParameterViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new ParameterView(
            array(
                'parameter' => $valueObject,
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof ParameterValue;
    }
}
