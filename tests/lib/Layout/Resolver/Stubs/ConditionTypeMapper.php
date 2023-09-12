<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\Stubs;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\MapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

final class ConditionTypeMapper implements MapperInterface
{
    public function getFormType(): string
    {
        return FormType::class;
    }

    public function getFormOptions(): array
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $builder): void {}
}
