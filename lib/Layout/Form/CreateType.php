<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
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

final class CreateType extends AbstractType
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Layout\Registry\LayoutTypeRegistryInterface
     */
    private $layoutTypeRegistry;

    /**
     * @var \Netgen\BlockManager\Locale\LocaleProviderInterface
     */
    private $localeProvider;

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
            [
                'label' => 'layout.type',
                'required' => true,
                'choices' => $this->layoutTypeRegistry->getLayoutTypes(true),
                'choice_value' => 'identifier',
                'choice_label' => function ($layoutType) {
                    return $layoutType->getName();
                },
                'choice_translation_domain' => false,
                'expanded' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
                'property_path' => 'layoutType',
            ] + $this->getChoicesAsValuesOption()
        );

        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'layout.name',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ],
                'property_path' => 'name',
            ]
        );

        $builder->add(
            'mainLocale',
            ChoiceType::class,
            [
                'label' => 'layout.main_locale',
                'required' => true,
                'choices' => array_flip($this->localeProvider->getAvailableLocales()),
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                    new Constraints\Locale(),
                ],
                'property_path' => 'mainLocale',
            ] + $this->getChoicesAsValuesOption()
        );

        $builder->add(
            'description',
            TextareaType::class,
            [
                'label' => 'layout.description',
                'required' => false,
                'constraints' => [
                    new Constraints\Type(['type' => 'string']),
                ],
                'property_path' => 'description',
            ]
        );

        $builder->add(
            'shared',
            CheckboxType::class,
            [
                'label' => 'layout.shared',
                'constraints' => [
                    new Constraints\NotNull(),
                ],
                'property_path' => 'shared',
            ]
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $layoutTypeFormConfig = $form['layoutType']->getConfig();

        $view['layoutType']->vars['layout_types'] = $layoutTypeFormConfig->getOption('choices');
    }
}
