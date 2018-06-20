<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\View\View\ParameterView;
use Netgen\BlockManager\View\ViewInterface;

final class ParameterViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        $view = new ParameterView($value);

        $view->setFallbackContext(ParameterView::CONTEXT_DEFAULT);

        return $view;
    }

    public function supports($value): bool
    {
        return $value instanceof Parameter;
    }
}
