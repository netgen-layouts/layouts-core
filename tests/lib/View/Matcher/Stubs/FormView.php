<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\Stubs;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\View\View\FormViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

final class FormView extends View implements FormViewInterface
{
    public function getForm(): FormInterface
    {
    }

    public function getFormType(): string
    {
        return 'form_type';
    }

    public function getFormView(): SymfonyFormView
    {
    }

    public function getIdentifier(): string
    {
        return 'form_view';
    }
}
