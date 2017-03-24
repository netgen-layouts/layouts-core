<?php

namespace Netgen\BlockManager\Collection\Query\Form;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\QueryUpdateStruct as QueryUpdateStructConstraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FullEditType extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('query');
        $resolver->setAllowedTypes('query', Query::class);
        $resolver->setAllowedTypes('data', QueryUpdateStruct::class);

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

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'inherit_data' => true,
                'property_path' => 'parameterValues',
                'parameter_collection' => $queryType,
                'label_prefix' => 'query.' . $queryType->getType(),
            )
        );
    }
}
