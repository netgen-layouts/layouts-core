<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;

final class TimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'from',
            DateTimeType::class,
            [
                'required' => false,
            ]
        );

        $builder->add(
            'to',
            DateTimeType::class,
            [
                'required' => false,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'ngbm_condition_type_time';
    }
}
