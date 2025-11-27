<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type\DataMapper;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Symfony\Component\Form\DataMapperInterface;
use Traversable;

/**
 * Mapper used to convert to and from the LinkValue object to the Symfony form structure.
 */
final class LinkDataMapper implements DataMapperInterface
{
    public function __construct(
        private ParameterDefinition $parameterDefinition,
    ) {}

    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (!$viewData instanceof LinkValue) {
            return;
        }

        $forms = [...$forms];

        $forms['link_type']->setData($viewData->linkType);
        $forms['link_suffix']->setData($viewData->linkSuffix);
        $forms['new_window']->setData($viewData->newWindow);

        $linkType = $viewData->linkType->value ?? '';

        if (isset($forms[$linkType])) {
            $forms[$linkType]->setData($viewData->link);
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        $forms = [...$forms];

        $linkType = $forms['link_type']->getData()->value ?? '';

        $viewData = null;
        if ($linkType !== '') {
            $viewData = [
                'link_type' => $linkType,
                'link' => isset($forms[$linkType]) ? $forms[$linkType]->getData() : '',
                'link_suffix' => $forms['link_suffix']->getData() ?? '',
                'new_window' => (bool) $forms['new_window']->getData(),
            ];
        }

        $viewData = $this->parameterDefinition->type->fromHash($this->parameterDefinition, $viewData);
    }
}
