<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\View\View\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Symfony\Component\Form\FormInterface>
 */
final class FormViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): FormView
    {
        return new FormView($value);
    }

    public function supports(object $value): bool
    {
        return $value instanceof FormInterface;
    }
}
