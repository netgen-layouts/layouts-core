<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Form;

use Symfony\Component\Form\DataMapperInterface;

use function iterator_to_array;

final class CollectionDataMapper implements DataMapperInterface
{
    public function mapDataToForms($viewData, $forms): void
    {
        $forms = iterator_to_array($forms);

        $forms['offset']->setData($viewData->offset);
        $forms['limit']->setData($viewData->limit !== 0 ? $viewData->limit : null);
    }

    public function mapFormsToData($forms, &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $limit = $forms['limit']->getData();

        $viewData->offset = (int) $forms['offset']->getData();
        $viewData->limit = $limit !== null ? (int) $limit : 0;
    }
}
