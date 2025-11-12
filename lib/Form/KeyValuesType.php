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

        $resolver
            ->define('key_name')
            ->required()
            ->allowedTypes('string');

        $resolver
            ->define('key_label')
            ->required()
            ->allowedTypes('string');

        $resolver
            ->define('values_name')
            ->required()
            ->allowedTypes('string');

        $resolver
            ->define('values_label')
            ->required()
            ->allowedTypes('string');

        $resolver
            ->define('values_type')
            ->required()
            ->allowedTypes('string');

        $resolver
            ->define('values_options')
            ->required()
            ->default([])
            ->allowedTypes('array');

        $resolver
            ->define('values_constraints')
            ->required()
            ->default([])
            ->allowedTypes(sprintf('%s[]', Constraint::class));
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
                    constraints: $options['values_constraints'],
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
