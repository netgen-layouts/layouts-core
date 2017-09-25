<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

final class RemoveLocaleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('layout'));
        $resolver->setAllowedTypes('layout', Layout::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $localeBundle = Intl::getLocaleBundle();

        $locales = array();
        foreach ($options['layout']->getAvailableLocales() as $locale) {
            if ($locale !== $options['layout']->getMainLocale()) {
                $locales[$localeBundle->getLocaleName($locale) . ' (' . $locale . ')'] = $locale;
            }
        }

        $builder->add(
            'locales',
            ChoiceType::class,
            array(
                'required' => true,
                'choices' => $locales,
                'choices_as_values' => true,
                'expanded' => true,
                'multiple' => true,
                'constraints' => array(
                    new Constraints\Type(array('type' => 'array')),
                    new Constraints\All(
                        array(
                            'constraints' => array(
                                new Constraints\NotBlank(),
                                new Constraints\Type(array('type' => 'string')),
                                new Constraints\Locale(),
                            ),
                        )
                    ),
                ),
            )
        );
    }
}
