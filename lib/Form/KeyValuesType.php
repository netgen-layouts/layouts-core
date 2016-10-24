<?php

namespace Netgen\BlockManager\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class KeyValuesType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(
            array(
                'key_name',
                'key_label',
                'values_name',
                'values_label',
                'values_type',
                'values_constraints',
            )
        );

        $resolver->setAllowedTypes('key_name', 'string');
        $resolver->setAllowedTypes('key_label', 'string');
        $resolver->setAllowedTypes('values_name', 'string');
        $resolver->setAllowedTypes('values_label', 'string');
        $resolver->setAllowedTypes('values_type', 'string');
        $resolver->setAllowedTypes('values_constraints', 'array');

        $resolver->setDefault('values_constraints', array());
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            $options['key_name'],
            TextType::class,
            array(
                'required' => true,
                'label' => $options['key_label'],
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
            )
        );

        $valueConstraints = array();
        if ($options['required']) {
            $valueConstraints[] = new Constraints\NotBlank();
        }

        if (!empty($options['values_constraints'])) {
            $valueConstraints[] = new Constraints\All(
                array(
                    'constraints' => $options['values_constraints'],
                )
            );
        }

        $builder->add(
            $options['values_name'],
            CollectionType::class,
            array(
                'required' => $options['required'],
                'label' => $options['values_label'],
                'constraints' => $valueConstraints,
                'entry_type' => $options['values_type'],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
            )
        );
    }

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['key_name'] = $options['key_name'];
        $view->vars['values_name'] = $options['values_name'];
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'ngbm_key_values';
    }
}
