<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Validator\Constraint\Locale as LocaleConstraint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class SetMainLocaleType extends AbstractType
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

        $builder->add(
            'mainLocale',
            ChoiceType::class,
            [
                'data' => $options['layout']->getMainLocale(),
                'required' => true,
                'choices' => $options['layout']->getAvailableLocales(),
                'choice_label' => function ($value) use ($options, $localeBundle) {
                    $localeName = $localeBundle->getLocaleName($value) . ' (' . $value . ')';
                    if ($value === $options['layout']->getMainLocale()) {
                        return $localeName . ' - main';
                    }

                    return $localeName;
                },
                'expanded' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                    new LocaleConstraint(),
                ],
            ] + $this->getChoicesAsValuesOption()
        );
    }
}
