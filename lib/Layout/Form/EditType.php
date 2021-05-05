<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Form;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\Validator\Constraint\LayoutName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class EditType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired('layout');
        $resolver->setAllowedTypes('layout', Layout::class);
        $resolver->setAllowedTypes('data', LayoutUpdateStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'layout.name',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new LayoutName(
                        [
                            'excludedLayoutId' => $options['layout']->getId(),
                        ],
                    ),
                ],
                'property_path' => 'name',
            ],
        );

        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'layout.description',
                'required' => false,
                'constraints' => [
                    new Constraints\Type(['type' => 'string']),
                ],
                'property_path' => 'description',
                'empty_data' => '',
            ],
        );
    }
}
