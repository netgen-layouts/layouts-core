<?php

namespace Netgen\BlockManager\Block\Form;

use Symfony\Component\Form\FormBuilderInterface;

class DesignEditType extends EditType
{
    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $options['blockDefinition'];

        $this->addViewTypeForm($builder, $options);
        $this->addParametersForm(
            $builder,
            $options,
            $blockDefinition->getConfig()->getForm('design')->getParameters()
        );
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
        return 'block_design_edit';
    }
}
