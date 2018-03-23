<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\View\View\ParameterView;

final class ParameterViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = array())
    {
        $view = new ParameterView(
            array(
                'parameter' => $value,
            )
        );

        $view->setFallbackContext(ParameterView::CONTEXT_DEFAULT);

        return $view;
    }

    public function supports($value)
    {
        return $value instanceof Parameter;
    }
}
