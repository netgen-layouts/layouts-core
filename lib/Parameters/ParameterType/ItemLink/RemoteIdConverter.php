<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType\ItemLink;

use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;

use function is_array;
use function parse_url;
use function str_replace;

final class RemoteIdConverter
{
    private const string NULL_LINK = 'null://0';

    public function __construct(
        private CmsItemLoaderInterface $cmsItemLoader,
    ) {}

    /**
     * Converts the value_type://value format of the item reference to value_type://remote_id.
     * This is useful for various export/import operations between different systems.
     *
     * If the conversion cannot be done, (for example, because item does not exist), a reference to
     * the so called null item will be returned.
     */
    public function convertToRemoteId(string $link): string
    {
        $parsedLink = parse_url($link);

        if (!is_array($parsedLink) || !isset($parsedLink['host'], $parsedLink['scheme'])) {
            return self::NULL_LINK;
        }

        $item = $this->cmsItemLoader->load($parsedLink['host'], str_replace('-', '_', $parsedLink['scheme']));
        if ($item instanceof NullCmsItem) {
            return self::NULL_LINK;
        }

        return $parsedLink['scheme'] . '://' . $item->remoteId;
    }

    /**
     * Converts the value_type://remote_id format of the item reference to value_type://value.
     * This is useful for various export/import operations between different systems.
     *
     * If the conversion cannot be done, (for example, because item does not exist), a reference to
     * the so called null item will be returned.
     */
    public function convertFromRemoteId(string $link): string
    {
        $parsedLink = parse_url($link);

        if (!is_array($parsedLink) || !isset($parsedLink['host'], $parsedLink['scheme'])) {
            return self::NULL_LINK;
        }

        $item = $this->cmsItemLoader->loadByRemoteId($parsedLink['host'], str_replace('-', '_', $parsedLink['scheme']));
        if ($item instanceof NullCmsItem) {
            return self::NULL_LINK;
        }

        return $parsedLink['scheme'] . '://' . $item->value;
    }
}
