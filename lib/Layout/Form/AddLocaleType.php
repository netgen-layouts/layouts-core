<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class AddLocaleType extends AbstractType
{
    use ChoicesAsValuesTrait;

    /**
     * @var \Netgen\BlockManager\Locale\LocaleProviderInterface
     */
    private $localeProvider;

    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(['layout']);
        $resolver->setAllowedTypes('layout', Layout::class);

        $resolver->setDefault('translation_domain', 'ngbm_forms');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $newLocales = [];
        $layoutLocales = $options['layout']->getAvailableLocales();
        $localeBundle = Intl::getLocaleBundle();

        foreach ($this->localeProvider->getAvailableLocales() as $locale => $localeName) {
            if (!in_array($locale, $layoutLocales, true)) {
                $newLocales[$localeName . ' (' . $locale . ')'] = $locale;
            }
        }

        $builder->add(
            'locale',
            ChoiceType::class,
            [
                'label' => 'layout.add_locale.locale',
                'required' => true,
                'choices' => $newLocales,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                    new Constraints\Locale(),
                ],
            ] + $this->getChoicesAsValuesOption()
        );

        $builder->add(
            'sourceLocale',
            ChoiceType::class,
            [
                'data' => $options['layout']->getMainLocale(),
                'label' => 'layout.add_locale.source_locale',
                'required' => true,
                'choices' => $layoutLocales,
                'choice_label' => function ($value, $key, $index) use ($options, $localeBundle) {
                    $localeName = $localeBundle->getLocaleName($value) . ' (' . $value . ')';
                    if ($value === $options['layout']->getMainLocale()) {
                        return $localeName . ' - main';
                    }

                    return $localeName;
                },
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                    new Constraints\Locale(),
                ],
            ] + $this->getChoicesAsValuesOption()
        );
    }
}
