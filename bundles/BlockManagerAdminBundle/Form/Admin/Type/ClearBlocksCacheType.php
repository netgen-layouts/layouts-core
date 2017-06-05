<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClearBlocksCacheType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('blocks'));
        $resolver->setAllowedTypes('blocks', 'array');
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
            'blocks',
            ChoiceType::class,
            array(
                'choices' => $options['blocks'],
                'choice_value' => 'id',
                'choice_label' => function ($block) {
                    $blockName = $block->getName();

                    return !empty($blockName) ? $blockName : ' ';
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
        $blocks = array();
        foreach ($options['blocks'] as $block) {
            $blocks[$block->getId()] = $block;
        }

        $view->vars['blocks'] = $blocks;
    }
}
