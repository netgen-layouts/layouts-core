<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
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
        $this->addViewTypeForm($builder, $options);
        $this->addParametersForm(
            $builder,
            $options,
            array(BlockDefinitionHandler::GROUP_DESIGN)
        );
    }
}
