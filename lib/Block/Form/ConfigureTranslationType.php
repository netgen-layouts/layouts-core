<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class ConfigureTranslationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['block']);
        $resolver->setAllowedTypes('block', Block::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'translatable',
            CheckboxType::class,
            [
                'data' => $options['block']->isTranslatable(),
                'required' => false,
                'label' => 'block.configure_translation.translatable',
                'constraints' => [
                    new Constraints\NotNull(),
                ],
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['block'] = $options['block'];
    }
}
