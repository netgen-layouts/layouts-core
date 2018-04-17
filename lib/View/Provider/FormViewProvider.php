<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\View\View\FormView;
use Symfony\Component\Form\FormInterface;

final class FormViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = [])
    {
        return new FormView(
            [
                'form_object' => $value,
                'form' => $value->createView(),
            ]
        );
    }

    public function supports($value)
    {
        return $value instanceof FormInterface;
    }
}
