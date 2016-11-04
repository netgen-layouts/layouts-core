<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

class CompoundBooleanType extends ParametersType
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
                'reverse',
            )
        );

        $resolver->setAllowedTypes('reverse', 'bool');

        $resolver->setDefault('reverse', false);
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

                if (empty($data)) {
                    return;
                }

                $clearChildren = !isset($data['_self']) || !$data['_self'];
                if (isset($options['reverse']) && $options['reverse'] === true) {
                    $clearChildren = !$clearChildren;
                }

                if ($clearChildren) {
                    foreach ($data as $key => $value) {
                        if ($key !== '_self') {
                            $data[$key] = null;
                        }
                    }
                }

                $event->setData($data);
            }
        );

        $builder->add(
            '_self',
            CheckboxType::class,
            array(
                'required' => $builder->getRequired(),
                'label' => $options['label_prefix'] . '.' . $builder->getName(),
                'property_path' => $options['property_path_prefix'] . '[' . $builder->getName() . ']',
            )
        );

        parent::buildForm($builder, $options);
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

        $view->vars['reverse'] = $options['reverse'];
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
