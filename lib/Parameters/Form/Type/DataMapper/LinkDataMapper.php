<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type\DataMapper;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\Value\LinkValue;
use Symfony\Component\Form\DataMapperInterface;
use Traversable;

use function iterator_to_array;

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

        $forms = iterator_to_array($forms);

        $forms['link_type']->setData($viewData->getLinkType());
        $forms['link_suffix']->setData($viewData->getLinkSuffix());
        $forms['new_window']->setData($viewData->getNewWindow());

        $linkType = $viewData->getLinkType()->value ?? '';

        if (isset($forms[$linkType])) {
            $forms[$linkType]->setData($viewData->getLink());
        }
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

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

        $viewData = $this->parameterDefinition->getType()->fromHash($this->parameterDefinition, $viewData);
    }
}
