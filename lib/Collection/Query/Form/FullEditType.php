<?php

namespace Netgen\BlockManager\Collection\Query\Form;

use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class FullEditType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_forms';

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('query');
        $resolver->setAllowedTypes('query', Query::class);
        $resolver->setAllowedTypes('data', QueryUpdateStruct::class);
        $resolver->setDefault('translation_domain', self::TRANSLATION_DOMAIN);

        $resolver->setDefault('constraints', function (Options $options) {
            return array(
                new QueryUpdateStructConstraint(
                    array(
                        'payload' => $options['query'],
                    )
                ),
            );
        });
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Collection\QueryTypeInterface $queryType */
        $queryType = $options['query']->getQueryType();
        $parameters = $queryType->getParameters();

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'parameters' => $parameters,
                'label_prefix' => 'query.' . $queryType->getType(),
                'property_path_prefix' => 'parameters',
            )
        );
    }
}
