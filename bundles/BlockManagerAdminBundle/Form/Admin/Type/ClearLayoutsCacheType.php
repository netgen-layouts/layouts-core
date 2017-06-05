<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClearLayoutsCacheType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('layouts'));
        $resolver->setAllowedTypes('layouts', 'array');
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
            'layouts',
            ChoiceType::class,
            array(
                'choices' => $options['layouts'],
                'choice_value' => 'id',
                'choice_label' => function ($layout) {
                    $layoutName = $layout->getName();

                    return !empty($layoutName) ? $layoutName : ' ';
                },
                'choices_as_values' => true,
                'label' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'constraints' => array(
                    new NotBlank(),
                ),
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
        $layouts = array();
        foreach ($options['layouts'] as $layout) {
            $layouts[$layout->getId()] = $layout;
        }

        $view->vars['layouts'] = $layouts;
    }
}
