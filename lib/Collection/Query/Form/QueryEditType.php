<?php

namespace Netgen\BlockManager\Collection\Query\Form;

use Netgen\BlockManager\API\Values\QueryUpdateStruct;
use Netgen\BlockManager\Collection\QueryTypeInterface;
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
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\FormMapper\FormMapperInterface $parameterFormMapper
     */
    public function __construct(FormMapperInterface $parameterFormMapper)
    {
        $this->parameterFormMapper = $parameterFormMapper;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('queryType');
        $resolver->setAllowedTypes('queryType', QueryTypeInterface::class);
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
        /** @var \Netgen\BlockManager\Collection\QueryTypeInterface $queryType */
        $queryType = $options['queryType'];

        // We're grouping query parameters so they don't conflict with forms from query itself
        $parameterBuilder = $builder->create(
            'parameters',
            'form',
            array(
                'label' => 'query.edit.parameters',
                'inherit_data' => true,
            )
        );

        foreach ($queryType->getParameters() as $parameterName => $parameter) {
            $this->parameterFormMapper->mapParameter(
                $parameterBuilder,
                $parameter,
                $parameterName,
                'query.' . $queryType->getType()
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
