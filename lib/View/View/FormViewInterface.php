<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

interface FormViewInterface extends ViewInterface
{
    /**
     * Returns the form.
     */
    public FormInterface $form { get; }

    /**
     * Returns the form type.
     */
    public string $formType { get; }

    /**
     * Returns the form view.
     */
    public SymfonyFormView $formView { get; }
}
