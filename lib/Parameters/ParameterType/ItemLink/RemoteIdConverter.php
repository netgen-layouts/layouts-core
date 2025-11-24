<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\ParameterType\ItemLink;

use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Uri\InvalidUriException;
use Uri\Rfc3986\Uri;

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
        try {
            $uri = new Uri($link);
        } catch (InvalidUriException) {
            return self::NULL_LINK;
        }

        $scheme = $uri->getScheme() ?? '';
        $host = $uri->getHost() ?? '';

        if ($scheme === '' || $host === '') {
            return self::NULL_LINK;
        }

        $item = $this->cmsItemLoader->load($host, str_replace('-', '_', $scheme));
        if ($item instanceof NullCmsItem) {
            return self::NULL_LINK;
        }

        return $uri->getScheme() . '://' . $item->remoteId;
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
        try {
            $uri = new Uri($link);
        } catch (InvalidUriException) {
            return self::NULL_LINK;
        }

        $scheme = $uri->getScheme() ?? '';
        $host = $uri->getHost() ?? '';

        if ($scheme === '' || $host === '') {
            return self::NULL_LINK;
        }

        $item = $this->cmsItemLoader->loadByRemoteId($host, str_replace('-', '_', $scheme));
        if ($item instanceof NullCmsItem) {
            return self::NULL_LINK;
        }

        return $uri->getScheme() . '://' . $item->value;
    }
}
