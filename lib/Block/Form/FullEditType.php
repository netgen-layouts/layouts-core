<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Form;

use Netgen\Layouts\Form\TranslatableTypeTrait;
use Symfony\Component\Form\FormBuilderInterface;

final class FullEditType extends EditType
{
    use TranslatableTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $options['data']->locale;
        $mainLocale = $options['block']->getMainLocale();

        $this->addViewTypeForm($builder, $options);
        $this->addBlockNameForm($builder, $options);
        $this->addParametersForm($builder, $options);

        if ($locale !== $mainLocale) {
            $this->disableUntranslatableForms($builder);
        }
    }
}
