<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\View\View\FormView;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\Form\FormInterface;

final class FormViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        return new FormView(
            [
                'form_object' => $value,
                'form' => $value->createView(),
            ]
        );
    }

    public function supports($value): bool
    {
        return $value instanceof FormInterface;
    }
}
