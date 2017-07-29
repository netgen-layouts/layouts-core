<?php

namespace Netgen\BlockManager\Block\Form;

use Symfony\Component\Form\FormBuilderInterface;

class FullEditType extends EditType
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

        $this->addViewTypeForm($builder, $options);
        $this->addBlockNameForm($builder, $options);
        $this->addParametersForm($builder, $options);

        if ($locale !== $mainLocale) {
            $this->disableFormsOnNonMainLocale($builder);
        }
    }
}
