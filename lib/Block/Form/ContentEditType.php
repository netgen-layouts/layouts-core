<?php

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Symfony\Component\Form\FormBuilderInterface;

class ContentEditType extends EditType
{
    /**
     * Builds the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locale = $options['data']->locale;
        $mainLocale = $options['block']->getMainLocale();

        $this->addBlockNameForm($builder, $options);
        $this->addParametersForm(
            $builder,
            $options,
            array(BlockDefinitionHandler::GROUP_CONTENT)
        );

        if ($locale !== $mainLocale) {
            $this->disableFormsOnNonMainLocale($builder);
        }
    }
}
