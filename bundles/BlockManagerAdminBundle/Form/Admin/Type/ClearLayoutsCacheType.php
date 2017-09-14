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
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('layouts'));
        $resolver->setAllowedTypes('layouts', 'array');
    }

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
        $layouts = array();
        foreach ($options['layouts'] as $layout) {
            $layouts[$layout->getId()] = $layout;
        }

        $view->vars['layouts'] = $layouts;
    }
}
