<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Form;

use Symfony\Component\Form\DataMapperInterface;
use Traversable;

use function iterator_to_array;

final class CollectionDataMapper implements DataMapperInterface
{
    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        $forms = iterator_to_array($forms);

        $forms['offset']->setData($viewData->offset);
        $forms['limit']->setData($viewData->limit !== 0 ? $viewData->limit : null);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $limit = $forms['limit']->getData();

        $viewData->offset = (int) $forms['offset']->getData();
        $viewData->limit = $limit !== null ? (int) $limit : 0;
    }
}
