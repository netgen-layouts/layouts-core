<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type;

use Netgen\Layouts\Form\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'from',
            DateTimeType::class,
            [
                'required' => false,
                'use_datetime' => false,
                'label' => 'condition_type.time.from',
            ],
        );

        $builder->add(
            'to',
            DateTimeType::class,
            [
                'required' => false,
                'use_datetime' => false,
                'label' => 'condition_type.time.to',
            ],
        );
    }

    public function getBlockPrefix(): string
    {
        return 'nglayouts_condition_type_time';
    }
}
