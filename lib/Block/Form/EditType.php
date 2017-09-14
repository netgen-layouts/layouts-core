<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Block\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Form\TranslatableType;
use Netgen\BlockManager\Parameters\Form\Type\ParametersType;
use Netgen\BlockManager\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class EditType extends TranslatableType
{
    /**
     * @var array
     */
    protected $viewTypes = array();

    /**
     * @var array
     */
    protected $itemViewTypes = array();

    /**
     * @var array
     */
    protected $viewTypesByItemViewType = array();

    /**
     * @var array
     */
    protected $viewTypesByParameters = array();

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('block');
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);

        $resolver->setDefault('constraints', function (Options $options) {
            return array(
                new BlockUpdateStructConstraint(
                    array(
                        'payload' => $options['block'],
                    )
                ),
            );
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['block'] = $options['block'];
        $view->vars['parameter_view_types'] = $this->viewTypesByParameters;
    }

    /**
     * Adds view type and item view type forms to the provided form builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    protected function addViewTypeForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['block']->getDefinition();

        $this->processViewTypeConfig($blockDefinition);

        $builder->add(
            'view_type',
            ChoiceType::class,
            array(
                'label' => 'block.view_type',
                'choices' => array_flip($this->viewTypes),
                'choices_as_values' => true,
                'property_path' => 'viewType',
            )
        );

        $builder->add(
            'item_view_type',
            ChoiceType::class,
            array(
                'label' => 'block.item_view_type',
                'choices' => array_flip(call_user_func_array('array_merge', $this->itemViewTypes)),
                'choice_attr' => function ($value) {
                    return array(
                        'data-master' => implode(',', $this->viewTypesByItemViewType[$value]),
                    );
                },
                'choices_as_values' => true,
                'property_path' => 'itemViewType',
            )
        );
    }

    /**
     * Adds a name form to the provided form builders.
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
     * Adds the parameters form to the provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @param array $groups
     */
    protected function addParametersForm(FormBuilderInterface $builder, array $options, array $groups = array())
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['block']->getDefinition();

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'inherit_data' => true,
                'property_path' => 'parameterValues',
                'parameter_collection' => $blockDefinition,
                'label_prefix' => 'block.' . $blockDefinition->getIdentifier(),
                'groups' => $groups,
            )
        );
    }

    /**
     * Generates the list of valid view types for every item view type
     * and for every parameter, according to config provided by the block definition.
     *
     * These lists are used by the interface to hide and show item view types
     * and parameters based on selected view type.
     *
     * @todo Move this code somewhere else
     *
     * @param \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition
     */
    protected function processViewTypeConfig(BlockDefinitionInterface $blockDefinition)
    {
        $blockDefinitionParameters = array_keys($blockDefinition->getParameters());

        foreach ($blockDefinition->getConfig()->getViewTypes() as $viewType) {
            $this->viewTypes[$viewType->getIdentifier()] = $viewType->getName();

            foreach ($viewType->getItemViewTypes() as $itemViewType) {
                $this->itemViewTypes[$viewType->getIdentifier()][$itemViewType->getIdentifier()] = $itemViewType->getName();
                $this->viewTypesByItemViewType[$itemViewType->getIdentifier()][] = $viewType->getIdentifier();
            }

            $includedParameters = array();
            $excludedParameters = array();

            $validParameters = $viewType->getValidParameters();
            if (!is_array($validParameters)) {
                $includedParameters = $blockDefinitionParameters;
            } elseif (!empty($validParameters)) {
                foreach ($validParameters as $validParameter) {
                    strpos($validParameter, '!') === 0 ?
                        $excludedParameters[] = substr($validParameter, 1) :
                        $includedParameters[] = $validParameter;

                    if (empty($includedParameters)) {
                        $includedParameters = $blockDefinitionParameters;
                    }
                }
            }

            foreach ($includedParameters as $includedParameter) {
                if (!in_array($includedParameter, $excludedParameters, true)) {
                    $this->viewTypesByParameters[$includedParameter][] = $viewType->getIdentifier();
                }
            }
        }
    }
}
