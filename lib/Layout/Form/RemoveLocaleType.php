<?php

namespace Netgen\BlockManager\Layout\Form;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class RemoveLocaleType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired(array('layout'));
        $resolver->setAllowedTypes('layout', Layout::class);
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $localeBundle = Intl::getLocaleBundle();

        $builder->add(
            'locales',
            ChoiceType::class,
            array(
                'required' => true,
                'choices' => $options['layout']->getAvailableLocales(),
                'choice_label' => function ($value, $key, $index) use ($options, $localeBundle) {
                    $localeName = $localeBundle->getLocaleName($value) . ' (' . $value . ')';
                    if ($value === $options['layout']->getMainLocale()) {
                        return $localeName . ' - main';
                    }

                    return $localeName;
                },
                'choice_attr' => function ($value, $key, $index) use ($options) {
                    if ($value === $options['layout']->getMainLocale()) {
                        return array('disabled' => true);
                    }

                    return array();
                },
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
