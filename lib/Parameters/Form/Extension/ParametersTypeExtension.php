<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Extension;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form extension attaches the parameter being edited
 * to the form used to edit the parameter.
 */
final class ParametersTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType(): string
    {
        return FormType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('ngl_parameter_definition');
        $resolver->setAllowedTypes('ngl_parameter_definition', ParameterDefinition::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['ngl_parameter_definition'])) {
            $view->vars['ngl_parameter_definition'] = $options['ngl_parameter_definition'];
        }
    }
}
