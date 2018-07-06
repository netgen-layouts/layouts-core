<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\TranslatableTypeTrait;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class CollectionEditType extends AbstractType
{
    use TranslatableTypeTrait;

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('collection');
        $resolver->setAllowedTypes('collection', Collection::class);
        $resolver->setAllowedTypes('data', CollectionUpdateStruct::class);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['collection'] = $options['collection'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'offset',
            IntegerType::class,
            [
                'label' => 'collection.offset',
                'property_path' => 'offset',
                // Manual collections do not support the offset
                'disabled' => !($options['collection']->hasQuery()),
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'int']),
                    new Constraints\GreaterThanOrEqual(['value' => 0]),
                ],
            ]
        );

        $builder->add(
            'limit',
            IntegerType::class,
            [
                'label' => 'collection.limit',
                'property_path' => 'limit',
                'constraints' => [
                    new Constraints\Type(['type' => 'int']),
                    new Constraints\GreaterThanOrEqual(['value' => 0]),
                ],
            ]
        );

        $builder->setDataMapper(new CollectionDataMapper());
    }
}
