<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
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

        $resolver->setRequired(array('layout'));
        $resolver->setAllowedTypes('layout', Layout::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $localeBundle = Intl::getLocaleBundle();

        $builder->add(
            'mainLocale',
            ChoiceType::class,
            array(
                'data' => $options['layout']->getMainLocale(),
                'required' => true,
                'choices' => $options['layout']->getAvailableLocales(),
                'choice_label' => function ($value, $key, $index) use ($options, $localeBundle) {
                    $localeName = $localeBundle->getLocaleName($value) . ' (' . $value . ')';
                    if ($value === $options['layout']->getMainLocale()) {
                        return $localeName . ' - main';
                    }

                    return $localeName;
                },
                'expanded' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new Constraints\Locale(),
                ),
            ) + $this->getChoicesAsValuesOption()
        );
    }
}
