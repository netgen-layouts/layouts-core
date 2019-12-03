<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Mapper used to create a Symfony form for editing a parameter type.
 */
interface MapperInterface
{
    /**
     * Returns the form type for the parameter.
     */
    public function getFormType(): string;

    /**
     * Maps parameter options to Symfony form options.
     *
     * @return mixed[]
     */
    public function mapOptions(ParameterDefinition $parameterDefinition): array;

    /**
     * Allows the mapper to do any kind of processing to created form.
     */
    public function handleForm(FormBuilderInterface $form, ParameterDefinition $parameterDefinition): void;
}
