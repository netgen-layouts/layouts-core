<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

abstract class EditType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_block_forms';

    /**
     * @var array
     */
    protected $choicesAsValues;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // choices_as_values is deprecated on Symfony >= 3.1,
        // while on previous versions needs to be set to true
        $this->choicesAsValues = Kernel::VERSION_ID < 30100 ?
            array('choices_as_values' => true) :
            array();
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('blockDefinition');
        $resolver->setAllowedTypes('blockDefinition', BlockDefinitionInterface::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);
        $resolver->setDefault('translation_domain', self::TRANSLATION_DOMAIN);
    }

    /**
     * Adds view type and item view type form children to form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    protected function addViewTypeForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['blockDefinition'];

        $choices = array();
        foreach ($blockDefinition->getConfig()->getViewTypes() as $viewType) {
            $choices[$viewType->getName()] = $viewType->getIdentifier();
        }

        $builder->add(
            'view_type',
            ChoiceType::class,
            array(
                'label' => 'block.view_type',
                'choices' => $choices,
                'property_path' => 'viewType',
            ) + $this->choicesAsValues
        );

        $itemViewTypeBuilder = function (FormInterface $form, BlockDefinitionInterface $blockDefinition, $viewType) {
            $choices = array();
            if ($blockDefinition->getConfig()->hasViewType($viewType)) {
                $viewType = $blockDefinition->getConfig()->getViewType($viewType);

                foreach ($viewType->getItemViewTypes() as $itemViewType) {
                    $choices[$itemViewType->getName()] = $itemViewType->getIdentifier();
                }
            }

            $form->add(
                'item_view_type',
                ChoiceType::class,
                array(
                    'label' => 'block.item_view_type',
                    'choices' => $choices,
                    'property_path' => 'itemViewType',
                ) + $this->choicesAsValues
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($itemViewTypeBuilder) {
                $form = $event->getForm();

                $itemViewTypeBuilder(
                    $form,
                    $form->getConfig()->getOption('blockDefinition'),
                    $event->getData()->viewType
                );
            }
        );

        $builder->get('view_type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($itemViewTypeBuilder) {
                $form = $event->getForm()->getParent();

                $itemViewTypeBuilder(
                    $form,
                    $form->getConfig()->getOption('blockDefinition'),
                    $event->getData()
                );
            }
        );
    }

    /**
     * Adds a name form child to form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    protected function addBlockNameForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            array(
                'label' => 'block.name',
                'property_path' => 'name',
                // null and empty string have different meanings for name
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            )
        );
    }

    /**
     * Adds parameters form child to form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @param array $parameterNames
     */
    protected function addParametersForm(FormBuilderInterface $builder, array $options, array $parameterNames = null)
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['blockDefinition'];
        $parameters = $blockDefinition->getHandler()->getParameters();

        if ($parameterNames !== null) {
            $parameters = array_intersect_key($parameters, array_flip($parameterNames));
        }

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'parameters' => $parameters,
                'label_prefix' => 'block.' . $blockDefinition->getIdentifier(),
                'property_path' => 'parameters',
            )
        );
    }
}
