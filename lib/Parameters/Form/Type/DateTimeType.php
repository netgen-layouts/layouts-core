<?php

namespace Netgen\BlockManager\Parameters\Form\Type;

use DateTime;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as BaseDateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('widget', 'single_text');
        //$resolver->setDefault('format', DateTime::ISO8601);
        $resolver->setDefault('input', 'datetime');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //$view->vars['type'] = 'datetime-local';
        $view->vars['type'] = 'datetime';
    }

    public function getParent()
    {
        return BaseDateTimeType::class;
    }
}
