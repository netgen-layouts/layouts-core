<?php

namespace Netgen\BlockManager\BlockDefinition\Form;

use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\BlockDefinition\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class TitleInlineType extends AbstractType
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
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);
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

        $parameters = $blockDefinition->getParameters();
        $parameterConstraints = $blockDefinition->getParameterConstraints();

        $builder->add(
            'tag',
            'hidden',
            array(
                'required' => $parameters['tag']->isRequired(),
                'property_path' => 'parameters[tag]',
                'constraints' => isset($parameterConstraints['tag']) && is_array($parameterConstraints['tag']) ?
                    $parameterConstraints['tag'] :
                    null,
            )
        );

        $builder->add(
            'title',
            'hidden',
            array(
                'required' => $parameters['title']->isRequired(),
                'property_path' => 'parameters[title]',
                'constraints' => isset($parameterConstraints['title']) && is_array($parameterConstraints['title']) ?
                    $parameterConstraints['title'] :
                    null,
            )
        );
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
       return 'title_inline';
   }
}
