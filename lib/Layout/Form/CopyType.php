<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class CopyType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'layout.name',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ),
            )
        );
    }
}
