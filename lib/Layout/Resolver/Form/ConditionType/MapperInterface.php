<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\ConditionType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Implementations of this interface provide all info to create Symfony forms
 * used to create/edit condition objects.
 */
interface MapperInterface
{
    /**
     * Returns the form type that will be used to edit the value of this condition type.
     */
    public function getFormType(): string;

    /**
     * Returns the form options.
     *
     * @return array<string, mixed>
     */
    public function getFormOptions(): array;

    /**
     * Handles the form for the condition type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     */
    public function handleForm(FormBuilderInterface $builder): void;
}
