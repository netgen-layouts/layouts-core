<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

final class FormView extends View implements FormViewInterface
{
    public string $identifier {
        get => 'form';
    }

    public FormInterface $form {
        get => $this->getParameter('form_object');
    }

    public string $formType {
        get => $this->form->getConfig()->getType()->getInnerType()::class;
    }

    public SymfonyFormView $formView {
        get => $this->getParameter('form');
    }

    public function __construct(FormInterface $form)
    {
        $this
            ->addInternalParameter('form_object', $form)
            ->addInternalParameter('form', $form->createView());
    }
}
