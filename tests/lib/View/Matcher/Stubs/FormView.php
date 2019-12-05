<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\Stubs;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\View\View;
use Netgen\Layouts\View\View\FormViewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as SymfonyFormView;

final class FormView extends View implements FormViewInterface
{
    public function getForm(): FormInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function getFormType(): string
    {
        return 'form_type';
    }

    public function getFormView(): SymfonyFormView
    {
        throw new RuntimeException('Not implemented');
    }

    public static function getIdentifier(): string
    {
        return 'form';
    }
}
