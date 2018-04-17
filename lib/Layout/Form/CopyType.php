<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutCopyStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class CopyType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('layout');
        $resolver->setAllowedTypes('layout', Layout::class);
        $resolver->setAllowedTypes('data', LayoutCopyStruct::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'layout.name',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ],
                'property_path' => 'name',
            ]
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
                // null and empty string have different meanings for description
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            ]
        );
    }
}
