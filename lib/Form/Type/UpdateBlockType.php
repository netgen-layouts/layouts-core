<?php

namespace Netgen\BlockManager\Form\Type;

use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use RuntimeException;

class UpdateBlockType extends AbstractType
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        ConfigurationInterface $configuration
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->configuration = $configuration;
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('block');
        $resolver->setAllowedTypes('block', 'Netgen\BlockManager\API\Values\Page\Block');
        $resolver->setAllowedTypes('data', 'Netgen\BlockManager\API\Values\BlockUpdateStruct');
    }

    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
            $options['block']->getDefinitionIdentifier()
        );

        $blockConfig = $this->configuration->getBlockConfig(
            $blockDefinition->getIdentifier()
        );

        $choices = array();
        foreach ($blockConfig['view_types'] as $identifier => $viewType) {
            $choices[$identifier] = $viewType['name'];
        }

        $builder->add(
            'view_type',
            'choice',
            array(
                'label' => 'View type',
                'choices' => $choices,
                'property_path' => 'viewType',
            )
        );

        $builder->add(
            'name',
            'text',
            array(
                'label' => 'Name',
                'property_path' => 'name',
                // null and empty string have different meanings for name
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            )
        );

        // We're grouping block parameters so they don't conflict with forms from block itself
        $parameterBuilder = $builder->create(
            'parameters',
            'form',
            array(
                'label' => 'Parameters',
                'inherit_data' => true,
            )
        );

        $parameters = $blockDefinition->getParameters();
        $parameterNames = $blockDefinition->getParameterNames();
        $parameterConstraints = $blockDefinition->getParameterConstraints();

        foreach ($parameters as $parameterIdentifier => $blockParameter) {
            $parameterBuilder->add(
                $parameterIdentifier,
                $blockParameter->getFormType(),
                array(
                    'label' => isset($parameterNames[$parameterIdentifier]) ?
                        $parameterNames[$parameterIdentifier] :
                        null,
                    'property_path' => 'parameters[' . $parameterIdentifier . ']',
                    'constraints' => $parameterConstraints[$parameterIdentifier] !== false ?
                        $parameterConstraints[$parameterIdentifier] :
                        null,
                ) + $blockParameter->mapFormTypeOptions()
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
       return 'block_update';
   }
}
