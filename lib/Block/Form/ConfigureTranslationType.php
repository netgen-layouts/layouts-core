<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ConfigureTranslationType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('block'));
        $resolver->setAllowedTypes('block', Block::class);

        $resolver->setDefault('translation_domain', 'ngbm_forms');
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
            'translatable',
            CheckboxType::class,
            array(
                'data' => $options['block']->isTranslatable(),
                'required' => false,
                'label' => 'block.configure_translation.translatable',
                'constraints' => array(
                    new Constraints\NotNull(),
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
        $view->vars['block'] = $options['block'];
    }
}
