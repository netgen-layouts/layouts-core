<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Form\Admin\Type;

use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ClearBlocksCacheType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('blocks'));
        $resolver->setAllowedTypes('blocks', 'array');
    }

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
                'translation_domain' => false,
                'choice_translation_domain' => false,
                'choices_as_values' => true,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'constraints' => array(
                    new NotBlank(),
                ),
            )
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $blocks = array();
        foreach ($options['blocks'] as $block) {
            $blocks[$block->getId()] = $block;
        }

        $view->vars['blocks'] = $blocks;
    }
}
