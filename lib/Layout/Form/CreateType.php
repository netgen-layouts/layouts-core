<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Page\LayoutCreateStruct;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints;

class CreateType extends AbstractType
{
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
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setAllowedTypes('data', LayoutCreateStruct::class);
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
                'required' => true,
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
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ),
                'property_path' => 'name',
            )
        );

        $builder->add(
            'shared',
            CheckboxType::class,
            array(
                'label' => 'layout.shared',
                'constraints' => array(
                    new Constraints\NotNull(),
                ),
                'property_path' => 'shared',
            )
        );
    }
}
