<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Form;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\BlockUpdateStruct;
use Netgen\Layouts\Block\BlockDefinitionInterface;
use Netgen\Layouts\Form\AbstractType;
use Netgen\Layouts\Parameters\Form\Type\ParametersType;
use Netgen\Layouts\Validator\Constraint\Structs\BlockUpdateStruct as BlockUpdateStructConstraint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class EditType extends AbstractType
{
    /**
     * @var array
     */
    private $viewTypes = [];

    /**
     * @var array
     */
    private $itemViewTypes = [];

    /**
     * @var array
     */
    private $viewTypesByItemViewType = [];

    /**
     * @var array
     */
    private $viewTypesByParameters = [];

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('block');
        $resolver->setAllowedTypes('block', Block::class);
        $resolver->setAllowedTypes('data', BlockUpdateStruct::class);

        $resolver->setDefault(
            'constraints',
            static function (Options $options): array {
                return [
                    new BlockUpdateStructConstraint(
                        [
                            'payload' => $options['block'],
                        ]
                    ),
                ];
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['block'] = $options['block'];
        $view->vars['parameter_view_types'] = $this->viewTypesByParameters;
    }

    /**
     * Adds view type and item view type forms to the provided form builder.
     */
    protected function addViewTypeForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Netgen\Layouts\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['block']->getDefinition();

        $this->processViewTypeConfig($blockDefinition);

        $builder->add(
            'view_type',
            ChoiceType::class,
            [
                'label' => 'block.view_type',
                'choices' => array_flip($this->viewTypes),
                'property_path' => 'viewType',
            ]
        );

        $builder->add(
            'item_view_type',
            ChoiceType::class,
            [
                'label' => 'block.item_view_type',
                'choices' => array_flip(call_user_func_array('array_merge', $this->itemViewTypes)),
                'choice_attr' => function ($value): array {
                    return [
                        'data-master' => implode(',', $this->viewTypesByItemViewType[$value]),
                    ];
                },
                'property_path' => 'itemViewType',
            ]
        );
    }

    /**
     * Adds a name form to the provided form builders.
     */
    protected function addBlockNameForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'block.name',
                'property_path' => 'name',
                // null and empty string have different meanings for name
                // so we set the default value to a single space (instead of
                // an empty string) because of
                // https://github.com/symfony/symfony/issues/5906
                'empty_data' => ' ',
            ]
        );
    }

    /**
     * Adds the parameters form to the provided builder.
     */
    protected function addParametersForm(FormBuilderInterface $builder, array $options, array $groups = []): void
    {
        /** @var \Netgen\Layouts\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['block']->getDefinition();

        $builder->add(
            'parameters',
            ParametersType::class,
            [
                'label' => false,
                'inherit_data' => true,
                'property_path' => 'parameterValues',
                'parameter_definitions' => $blockDefinition,
                'label_prefix' => 'block.' . $blockDefinition->getIdentifier(),
                'groups' => $groups,
            ]
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
     */
    private function processViewTypeConfig(BlockDefinitionInterface $blockDefinition): void
    {
        $blockDefinitionParameters = array_keys($blockDefinition->getParameterDefinitions());

        foreach ($blockDefinition->getViewTypes() as $viewType) {
            $this->viewTypes[$viewType->getIdentifier()] = $viewType->getName();

            foreach ($viewType->getItemViewTypes() as $itemViewType) {
                $this->itemViewTypes[$viewType->getIdentifier()][$itemViewType->getIdentifier()] = $itemViewType->getName();
                $this->viewTypesByItemViewType[$itemViewType->getIdentifier()][] = $viewType->getIdentifier();
            }

            $includedParameters = [];
            $excludedParameters = [];

            $validParameters = $viewType->getValidParameters();
            if (!is_array($validParameters)) {
                $includedParameters = $blockDefinitionParameters;
            } elseif (count($validParameters) > 0) {
                foreach ($validParameters as $validParameter) {
                    mb_strpos($validParameter, '!') === 0 ?
                        $excludedParameters[] = mb_substr($validParameter, 1) :
                        $includedParameters[] = $validParameter;

                    if (count($includedParameters) === 0) {
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
