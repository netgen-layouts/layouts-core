<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Form;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Form\TranslatableTypeTrait;
use Symfony\Component\Form\FormBuilderInterface;

final class DesignEditType extends EditType
{
    use TranslatableTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['data']->locale;
        $mainLocale = $options['block']->getMainLocale();

        $this->addViewTypeForm($builder, $options);
        $this->addParametersForm(
            $builder,
            $options,
            [BlockDefinitionHandler::GROUP_DESIGN],
        );

        if ($locale !== $mainLocale) {
            $this->disableUntranslatableForms($builder);
        }
    }
}
