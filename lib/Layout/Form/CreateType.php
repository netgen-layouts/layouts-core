<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints;

class CreateType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_layout_forms';

    /**
     * @var \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     */
    public function __construct(LayoutTypeRegistryInterface $layoutTypeRegistry)
    {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setAllowedTypes('data', LayoutCreateStruct::class);
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
        $choices = array();
        foreach ($this->layoutTypeRegistry->getLayoutTypes() as $layoutType) {
            $choices[$layoutType->getName()] = $layoutType->getIdentifier();
        }

        $builder->add(
            'type',
            ChoiceType::class,
            array(
                'label' => 'layout.type',
                'choices' => $choices,
                'choices_as_values' => true,
                'expanded' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'property_path' => 'type',
            )
        );

        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'layout.name',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ),
                'property_path' => 'name',
            )
        );
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'layout_create';
    }
}
