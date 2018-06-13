<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class RemoveLocaleType extends AbstractType
{
    use ChoicesAsValuesTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['layout']);
        $resolver->setAllowedTypes('layout', Layout::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $localeBundle = Intl::getLocaleBundle();

        $locales = [];
        foreach ($options['layout']->getAvailableLocales() as $locale) {
            if ($locale !== $options['layout']->getMainLocale()) {
                $locales[$localeBundle->getLocaleName($locale) . ' (' . $locale . ')'] = $locale;
            }
        }

        $builder->add(
            'locales',
            ChoiceType::class,
            [
                'required' => true,
                'choices' => $locales,
                'expanded' => true,
                'multiple' => true,
                'constraints' => [
                    new Constraints\Type(['type' => 'array']),
                    new Constraints\All(
                        [
                            'constraints' => [
                                new Constraints\NotBlank(),
                                new Constraints\Type(['type' => 'string']),
                                new LocaleConstraint(),
                            ],
                        ]
                    ),
                ],
            ] + $this->getChoicesAsValuesOption()
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['layout'] = $options['layout'];
    }
}
