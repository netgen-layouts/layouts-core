<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class CreateType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    protected $layoutTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
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
        $builder->add(
            'layoutType',
            ChoiceType::class,
            array(
                'label' => 'layout.type',
                'required' => true,
                'choices' => $this->layoutTypeRegistry->getLayoutTypes(true),
                'choice_value' => 'identifier',
                'choice_label' => function ($layoutType) {
                    return $layoutType->getName();
                },
                'choice_translation_domain' => false,
                'choices_as_values' => true,
                'expanded' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
                'property_path' => 'layoutType',
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
            'description',
            TextareaType::class,
            array(
                'label' => 'layout.description',
                'required' => false,
                'constraints' => array(
                    new Constraints\Type(array('type' => 'string')),
                ),
                'property_path' => 'description',
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

    /**
     * Finishes the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view['layoutType']->vars['layout_types'] = $this->layoutTypeRegistry->getLayoutTypes(true);
    }
}
