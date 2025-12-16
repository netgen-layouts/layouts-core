<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\View\View\ParameterView;
use Netgen\Layouts\View\ViewInterface;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\Parameters\Parameter>
 */
final class ParameterViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): ParameterView
    {
        $view = new ParameterView($value);

        $view->fallbackContext = ViewInterface::CONTEXT_DEFAULT;

        return $view;
    }

    public function supports(object $value): bool
    {
        return $value instanceof Parameter;
    }
}
