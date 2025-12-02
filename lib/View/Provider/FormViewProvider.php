<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\View\View\FormView;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Symfony\Component\Form\FormInterface>
 */
final class FormViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        return new FormView($value);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof FormInterface;
    }
}
