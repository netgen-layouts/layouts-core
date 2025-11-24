<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Type\DataMapper;

use Symfony\Component\Form\DataMapperInterface;
use Traversable;
use Uri\InvalidUriException;
use Uri\Rfc3986\Uri;

use function is_string;
use function str_replace;

/**
 * Mapper used to convert to and from the "link" to an item in "value_type://value"
 * format to the Symfony form structure.
 */
final class ItemLinkDataMapper implements DataMapperInterface
{
    public function mapDataToForms(mixed $viewData, Traversable $forms): void
    {
        if (!is_string($viewData)) {
            return;
        }

        $forms = [...$forms];

        try {
            $uri = new Uri($viewData);
        } catch (InvalidUriException) {
            return;
        }

        $scheme = $uri->getScheme() ?? '';
        $host = $uri->getHost() ?? '';

        if ($scheme === '' || $host === '') {
            return;
        }

        $forms['item_type']->setData(str_replace('-', '_', $scheme));
        $forms['item_value']->setData($host);
    }

    public function mapFormsToData(Traversable $forms, mixed &$viewData): void
    {
        $forms = [...$forms];

        $itemValue = $forms['item_value']->getData() ?? '';
        $itemType = $forms['item_type']->getData() ?? '';

        $viewData = null;
        if ($itemValue !== '' && $itemType !== '') {
            $viewData = str_replace('_', '-', $itemType) . '://' . $itemValue;
        }
    }
}
