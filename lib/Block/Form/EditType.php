<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\API\Values\BlockUpdateStruct;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Parameters\Form\ParametersType;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Validator\Constraint\Parameters;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints;

abstract class EditType extends AbstractType
{
    const TRANSLATION_DOMAIN = 'ngbm_forms';

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

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('blockDefinition');
        $resolver->setAllowedTypes('blockDefinition', BlockDefinitionInterface::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);
        $resolver->setDefault('translation_domain', self::TRANSLATION_DOMAIN);
    }

    /**
     * Builds the form view.
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array(
            'parameter_view_types' => $this->viewTypesByParameters,
        ) + $view->vars;
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

        $itemViewTypeBuilder = function (FormInterface $form, $viewType) {
            $form->add(
                'item_view_type',
                ChoiceType::class,
                array(
                    'label' => 'block.item_view_type',
                    'choices' => array_flip(call_user_func_array('array_merge', $this->itemViewTypes)),
                    'choice_attr' => function ($value, $key, $index) {
                        return array(
                            'data-master' => implode(',', $this->viewTypesByItemViewType[$value]),
                        );
                    },
                    'choices_as_values' => true,
                    'constraints' => array(
                        new Constraints\NotBlank(),
                        new Constraints\Choice(
                            array(
                                'choices' => isset($this->itemViewTypes[$viewType]) ?
                                    array_flip($this->itemViewTypes[$viewType]) :
                                    array(),
                                'multiple' => false,
                                'strict' => true,
                            )
                        ),
                    ),
                    'property_path' => 'itemViewType',
                )
            );
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($builder, $itemViewTypeBuilder) {
                $itemViewTypeBuilder($event->getForm(), $event->getData()->viewType);
            }
        );

        $builder->get('view_type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($itemViewTypeBuilder) {
                $itemViewTypeBuilder($event->getForm()->getParent(), $event->getData());
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
     * @param array $groups
     */
    protected function addParametersForm(FormBuilderInterface $builder, array $options, array $groups = array())
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['blockDefinition'];
        $formParameters = array();

        foreach ($blockDefinition->getParameters() as $parameterName => $parameter) {
            if ($this->includeParameter($parameter, $groups)) {
                $formParameters[$parameterName] = $parameter;
            }
        }

        $builder->add(
            'parameters',
            ParametersType::class,
            array(
                'label' => false,
                'parameters' => $formParameters,
                'label_prefix' => 'block.' . $blockDefinition->getIdentifier(),
                'property_path_prefix' => 'parameters',
                'constraints' => array(
                    new Parameters(
                        array(
                            'parameters' => $blockDefinition->getParameters(),
                            'required' => false,
                        )
                    )
                ),
            )
        );
    }

    /**
     * Returns if the parameter will be included in the form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param array $groups
     *
     * @return bool
     */
    protected function includeParameter(ParameterInterface $parameter, array $groups = array())
    {
        $parameterGroups = $parameter->getGroups();

        if (empty($parameterGroups) && empty($groups)) {
            return true;
        }

        return !empty(array_intersect($parameterGroups, $groups));
    }

    /**
     * Processes the view type config and builds arrays used by the forms.
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

            $validParameters = $viewType->getValidParameters();
            if (!is_array($validParameters)) {
                $validParameters = $blockDefinitionParameters;
            }

            foreach ($validParameters as $validParameter) {
                $this->viewTypesByParameters[$validParameter][] = $viewType->getIdentifier();
            }
        }
    }
}
