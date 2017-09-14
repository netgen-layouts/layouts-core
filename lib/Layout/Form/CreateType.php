<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
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
     * @var \Netgen\BlockManager\Locale\LocaleProviderInterface
     */
    protected $localeProvider;

    public function __construct(
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        LocaleProviderInterface $localeProvider
    ) {
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->localeProvider = $localeProvider;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setAllowedTypes('data', LayoutCreateStruct::class);
    }

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
            'mainLocale',
            ChoiceType::class,
            array(
                'label' => 'layout.main_locale',
                'required' => true,
                'choices' => array_flip($this->localeProvider->getAvailableLocales()),
                'choices_as_values' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new Constraints\Locale(),
                ),
                'property_path' => 'mainLocale',
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

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $layoutTypeFormConfig = $form['layoutType']->getConfig();

        $view['layoutType']->vars['layout_types'] = $layoutTypeFormConfig->getOption('choices');
    }
}
