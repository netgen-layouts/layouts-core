<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\View\View\ParameterView;
use Netgen\Layouts\View\ViewInterface;

final class ParameterViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        $view = new ParameterView($value);

        $view->setFallbackContext(ViewInterface::CONTEXT_DEFAULT);

        return $view;
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof Parameter;
    }
}
