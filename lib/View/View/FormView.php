<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

final class FormView extends View implements FormViewInterface
{
    public function getForm(): FormInterface
    {
        return $this->parameters['form_object'];
    }

    public function getFormType(): string
    {
        return get_class($this->parameters['form_object']->getConfig()->getType()->getInnerType());
    }

    public function getFormView(): SymfonyFormView
    {
        return $this->parameters['form'];
    }

    public function getIdentifier(): string
    {
        return 'form_view';
    }
}
