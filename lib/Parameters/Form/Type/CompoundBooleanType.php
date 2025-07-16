<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_keys;
use function count;
use function is_array;

final class CompoundBooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setRequired(
            [
                'reverse',
            ],
        );

        $resolver->setAllowedTypes('reverse', 'bool');

        $resolver->setDefault('reverse', false);
        $resolver->setDefault('inherit_data', true);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            static function (FormEvent $event) use ($options): void {
                $data = $event->getData();

                if (!is_array($data) || count($data) === 0) {
                    return;
                }

                $clearChildren = !((bool) ($data['_self'] ?? false));
                if (($options['reverse'] ?? false) === true) {
                    $clearChildren = !$clearChildren;
                }

                if ($clearChildren) {
                    foreach (array_keys($data) as $key) {
                        if ($key !== '_self') {
                            $data[$key] = null;
                        }
                    }
                }

                $event->setData($data);
            },
        );

        $builder->add(
            '_self',
            CheckboxType::class,
            [
                'required' => $builder->getRequired(),
                'label' => $builder->getOption('label'),
                'property_path' => $builder->getPropertyPath(),
            ],
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['reverse'] = $options['reverse'];
    }

    public function getBlockPrefix(): string
    {
        return 'nglayouts_compound_boolean';
    }
}
