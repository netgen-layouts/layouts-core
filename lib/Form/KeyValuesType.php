<?php

declare(strict_types=1);

namespace Netgen\Layouts\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;

use function count;
use function sprintf;

final class KeyValuesType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired(
            [
                'key_name',
                'key_label',
                'values_name',
                'values_label',
                'values_type',
                'values_options',
                'values_constraints',
            ],
        );

        $resolver->setAllowedTypes('key_name', 'string');
        $resolver->setAllowedTypes('key_label', 'string');
        $resolver->setAllowedTypes('values_name', 'string');
        $resolver->setAllowedTypes('values_label', 'string');
        $resolver->setAllowedTypes('values_type', 'string');
        $resolver->setAllowedTypes('values_options', 'array');
        $resolver->setAllowedTypes('values_constraints', sprintf('%s[]', Constraint::class));

        $resolver->setDefault('values_options', []);
        $resolver->setDefault('values_constraints', []);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $options['key_name'],
            TextType::class,
            [
                'required' => true,
                'label' => $options['key_label'],
            ],
        );

        $valueConstraints = [];
        if (count($options['values_constraints']) > 0) {
            $valueConstraints = [
                new Constraints\All(
                    [
                        'constraints' => $options['values_constraints'],
                    ],
                ),
            ];
        }

        $builder->add(
            $options['values_name'],
            CollectionType::class,
            [
                'required' => $options['required'],
                'label' => $options['values_label'],
                'constraints' => $valueConstraints,
                'entry_type' => $options['values_type'],
                'entry_options' => $options['values_options'],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => $options['required'],
            ],
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['key_name'] = $options['key_name'];
        $view->vars['values_name'] = $options['values_name'];
    }

    public function getBlockPrefix(): string
    {
        return 'nglayouts_key_values';
    }
}
