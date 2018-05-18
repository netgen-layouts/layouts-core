<?php

namespace Netgen\BlockManager\Collection\Form;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Form\TranslatableType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class QueryEditType extends TranslatableType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('query');
        $resolver->setAllowedTypes('query', Query::class);
        $resolver->setAllowedTypes('data', QueryUpdateStruct::class);

        $resolver->setDefault('constraints', function (Options $options) {
            return [
                new QueryUpdateStructConstraint(
                    [
                        'payload' => $options['query'],
                    ]
                ),
            ];
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['query'] = $options['query'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['data']->locale;
        $mainLocale = $options['query']->getMainLocale();

        /** @var \Netgen\BlockManager\Collection\QueryType\QueryTypeInterface $queryType */
        $queryType = $options['query']->getQueryType();

        $builder->add(
            'parameters',
            ParametersType::class,
            [
                'label' => false,
                'inherit_data' => true,
                'property_path' => 'parameterValues',
                'parameter_definitions' => $queryType,
                'label_prefix' => 'query.' . $queryType->getType(),
            ]
        );

        if ($locale !== $mainLocale) {
            $this->disableFormsOnNonMainLocale($builder);
        }
    }
}
