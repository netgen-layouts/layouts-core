<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\NullCmsItem;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Netgen\Layouts\Item\UrlType;
use Throwable;
use Uri\InvalidUriException;
use Uri\Rfc3986\Uri;

use function count;
use function is_array;
use function is_string;
use function str_replace;

final class ItemRuntime
{
    public function __construct(
        private CmsItemLoaderInterface $cmsItemLoader,
        private UrlGeneratorInterface $urlGenerator,
        private ErrorHandlerInterface $errorHandler,
    ) {}

    /**
     * The method returns the full path of provided item.
     *
     * It accepts three kinds of references to the item:
     *
     * 1) URI with value_type://value format, e.g. type://42
     * 2) ID and value type as an array, e.g. [42, 'type']
     * 3) \Netgen\Layouts\Item\CmsItemInterface object
     *
     * @param string|array<int|string>|\Netgen\Layouts\Item\CmsItemInterface $item
     */
    public function getItemPath(string|array|CmsItemInterface $item): string
    {
        if (!$item instanceof CmsItemInterface) {
            $item = $this->getItem($item) ?? new NullCmsItem('item');
        }

        return $this->urlGenerator->generate($item, UrlType::Default);
    }

    /**
     * The method returns the full admin path of provided item.
     *
     * It accepts three kinds of references to the item:
     *
     * 1) URI with value_type://value format, e.g. type://42
     * 2) ID and value type as an array, e.g. [42, 'type']
     * 3) \Netgen\Layouts\Item\CmsItemInterface object
     *
     * @param string|array<int|string>|\Netgen\Layouts\Item\CmsItemInterface $item
     */
    public function getItemAdminPath(string|array|CmsItemInterface $item): string
    {
        if (!$item instanceof CmsItemInterface) {
            $item = $this->getItem($item) ?? new NullCmsItem('item');
        }

        return $this->urlGenerator->generate($item, UrlType::Admin);
    }

    /**
     * @param string|array<int|string> $value
     *
     * Loads the item from the provided reference
     */
    private function getItem(string|array $value): ?CmsItemInterface
    {
        try {
            if (is_array($value) && count($value) === 2) {
                return $this->cmsItemLoader->load($value[0], (string) $value[1]);
            }

            if (is_string($value)) {
                try {
                    $itemUri = new Uri($value);
                } catch (InvalidUriException) {
                    throw ItemException::invalidValue($value);
                }

                $scheme = $itemUri->getScheme() ?? '';
                $host = $itemUri->getHost() ?? '';

                if ($scheme === '' || $host === '') {
                    throw ItemException::invalidValue($value);
                }

                return $this->cmsItemLoader->load(
                    $host,
                    str_replace('-', '_', $scheme),
                );
            }

            throw ItemException::canNotLoadItem();
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        return null;
    }
}
