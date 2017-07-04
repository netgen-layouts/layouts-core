<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\View\View\FormView;
use Symfony\Component\Form\FormInterface;

class FormViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        return new FormView(
            array(
                'form_object' => $valueObject,
                'form' => $valueObject->createView(),
            )
        );
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof FormInterface;
    }
}
