<?php

declare(strict_types=1);

namespace Netgen\Layouts\Form;

use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\FormBuilderInterface;

trait TranslatableTypeTrait
{
    /**
     * Disables all inputs for parameters which are not translatable.
     */
    private function disableUntranslatableForms(FormBuilderInterface $builder): void
    {
        foreach ($builder as $form) {
            $disabled = !$form->getType()->getInnerType() instanceof ParametersType;

            $parameterDefinition = $form->getOption('ngl_parameter_definition');
            if ($parameterDefinition instanceof ParameterDefinition) {
                $disabled = $parameterDefinition->getOption('translatable') !== true;
            }

            $form->setDisabled($disabled);

            if ($parameterDefinition instanceof ParameterDefinition) {
                continue;
            }

            $this->disableUntranslatableForms($form);
        }
    }
}
