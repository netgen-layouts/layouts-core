<?php

namespace Netgen\BlockManager\Layout\Form;

use Symfony\Component\Form\AbstractType;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CopyType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_forms';

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('layout'));
        $resolver->setAllowedTypes('layout', Layout::class);

        $resolver->setDefault('translation_domain', self::TRANSLATION_DOMAIN);
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $layout = $options['layout'];

        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'layout.name',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new LayoutName(
                        array(
                            'excludedLayoutId' => $layout->getId(),
                        )
                    ),
                ),
            )
        );
    }
}
