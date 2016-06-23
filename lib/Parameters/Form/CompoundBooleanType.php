<?php

namespace Netgen\BlockManager\Parameters\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraint;

class CompoundBooleanType extends AbstractType
{
    const COMPOUND_GROUP = 'Compound';

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(
            array(
                'parameters',
                'label_prefix',
                'property_path_prefix',
                'checkbox_name',
                'checkbox_required',
                'checkbox_label',
                'checkbox_constraints',
                'checkbox_property_path',
            )
        );

        $resolver->setAllowedTypes('parameters', 'array');
        $resolver->setAllowedTypes('label_prefix', 'string');
        $resolver->setAllowedTypes('property_path_prefix', 'string');
        $resolver->setAllowedTypes('checkbox_name', 'string');
        $resolver->setAllowedTypes('checkbox_required', 'bool');
        $resolver->setAllowedTypes('checkbox_label', 'string');
        $resolver->setAllowedTypes('checkbox_constraints', 'array');
        $resolver->setAllowedTypes('checkbox_property_path', 'string');

        $resolver->setDefault('inherit_data', true);
        $resolver->setDefault('parameters', array());
        $resolver->setDefault('checkbox_name', '_self');
        $resolver->setDefault('checkbox_required', false);
        $resolver->setDefault('checkbox_constraints', array());

        $resolver->setDefault(
            'validation_groups',
            function (FormInterface $form) {
                $formName = $form->getName();
                $parameters = $form->getData()->getParameters();

                if (isset($parameters[$formName]) && $parameters[$formName]) {
                    return array(Constraint::DEFAULT_GROUP, self::COMPOUND_GROUP);
                }

                return array(Constraint::DEFAULT_GROUP);
            }
        );
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                $data = $event->getData();
                $checkbox = $options['checkbox_name'];

                if (empty($data) || empty($data['parameters'])) {
                    return;
                }

                if (!isset($data[$checkbox]) || !$data[$checkbox]) {
                    foreach ($data['parameters'] as $key => $value) {
                        $data['parameters'][$key] = null;
                    }
                }

                $event->setData($data);
            }
        );

        $builder->add(
            $options['checkbox_name'],
            CheckboxType::class,
            array(
                'label' => $options['checkbox_label'],
                'required' => $options['checkbox_required'],
                'property_path' => $options['checkbox_property_path'],
                'constraints' => $options['checkbox_constraints'],
            )
        );

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => $options['label'],
                'parameters' => $options['parameters'],
                'label_prefix' => $options['label_prefix'],
                'property_path_prefix' => $options['property_path_prefix'],
                'parameter_validation_groups' => array(self::COMPOUND_GROUP),
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

        $view->vars = array(
            'checkbox_name' => $options['checkbox_name'],
        ) + $view->vars;
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
        return 'ngbm_compound_boolean';
    }
}
