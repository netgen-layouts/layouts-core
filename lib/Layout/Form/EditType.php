<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\LayoutUpdateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class EditType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('layout');
        $resolver->setAllowedTypes('layout', Layout::class);
        $resolver->setAllowedTypes('data', LayoutUpdateStruct::class);
    }

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
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new LayoutName(
                        array(
                            'excludedLayoutId' => $options['layout']->getId(),
                        )
                    ),
                ),
                'property_path' => 'name',
            )
        );

        $builder->add(
            'description',
            TextareaType::class,
            array(
                'label' => 'layout.description',
                'required' => false,
                'constraints' => array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'property_path' => 'description',
                // null and empty string have different meanings for description
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            )
        );
    }
}
