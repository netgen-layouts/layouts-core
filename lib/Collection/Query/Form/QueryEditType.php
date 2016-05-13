<?php

namespace Netgen\BlockManager\Collection\Query\Form;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class QueryEditType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface
     */
    protected $parameterFormMapper;

    /**
     * @var \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface
     */
    protected $queryTypeRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface $parameterFormMapper
     * @param \Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface $queryTypeRegistry
     */
    public function __construct(
        FormMapperInterface $parameterFormMapper,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->parameterFormMapper = $parameterFormMapper;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('query');
        $resolver->setAllowedTypes('query', Query::class);
        $resolver->setAllowedTypes('data', QueryUpdateStruct::class);
        $resolver->setDefault('translation_domain', 'ngbm_forms');
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $queryType = $this->queryTypeRegistry->getQueryType(
            $options['query']->getType()
        );

        // We're grouping query parameters so they don't conflict with forms from query itself
        $parameterBuilder = $builder->create(
            'parameters',
            'form',
            array(
                'label' => 'query.edit.parameters',
                'inherit_data' => true,
            )
        );

        $parameterConstraints = $queryType->getParameterConstraints();

        foreach ($queryType->getParameters() as $parameterName => $parameter) {
            $this->parameterFormMapper->mapParameter(
                $parameterBuilder,
                $parameter,
                $parameterName,
                'query.' . $queryType->getType(),
                isset($parameterConstraints[$parameterName]) ?
                    $parameterConstraints[$parameterName] :
                    null
            );
        }

        $builder->add($parameterBuilder);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     *
     * @deprecated Deprecated since Symfony 2.8, to be removed in Symfony 3.0.
     *             Implemented in order not to trigger deprecation notices in Symfony <= 2.7
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefixes default to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix()
    {
        return 'query_edit';
    }
}
