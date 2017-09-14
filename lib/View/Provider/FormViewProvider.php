<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\View\View\FormView;
use Symfony\Component\Form\FormInterface;

class FormViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        return new FormView(
            array(
                'form_object' => $valueObject,
                'form' => $valueObject->createView(),
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof FormInterface;
    }
}
