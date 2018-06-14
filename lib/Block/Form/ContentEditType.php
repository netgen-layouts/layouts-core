<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Form;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Symfony\Component\Form\FormBuilderInterface;

final class ContentEditType extends EditType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['data']->locale;
        $mainLocale = $options['block']->getMainLocale();

        $this->addBlockNameForm($builder, $options);
        $this->addParametersForm(
            $builder,
            $options,
            [BlockDefinitionHandler::GROUP_CONTENT]
        );

        if ($locale !== $mainLocale) {
            $this->disableFormsOnNonMainLocale($builder);
        }
    }
}
