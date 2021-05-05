<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Form\Admin\Type;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ClearLayoutsCacheType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired(['layouts']);
        $resolver->setAllowedTypes('layouts', LayoutList::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'layouts',
            ChoiceType::class,
            [
                'choices' => $options['layouts'],
                'choice_name' => static fn (Layout $layout): string => $layout->getId()->toString(),
                'choice_value' => 'id',
                'choice_label' => 'name',
                'translation_domain' => false,
                'choice_translation_domain' => false,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'constraints' => [new NotBlank()],
            ],
        );

        $builder->get('layouts')->addModelTransformer(new LayoutListTransformer());
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \Netgen\Layouts\API\Values\Layout\Layout $layout */
        foreach ($options['layouts'] as $layout) {
            if (!isset($view['layouts'][$layout->getId()->toString()])) {
                continue;
            }

            $view['layouts'][$layout->getId()->toString()]->vars['layout'] = $layout;
        }
    }
}
