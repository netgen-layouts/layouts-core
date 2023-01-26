<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Exception\Item\ItemException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Item\CmsItemLoaderInterface;
use Netgen\Layouts\Item\UrlGeneratorInterface;
use Throwable;

use function count;
use function is_array;
use function is_string;
use function parse_url;
use function str_replace;

final class ItemRuntime
{
    private CmsItemLoaderInterface $cmsItemLoader;

    private UrlGeneratorInterface $urlGenerator;

    private ErrorHandlerInterface $errorHandler;

    public function __construct(
        CmsItemLoaderInterface $cmsItemLoader,
        UrlGeneratorInterface $urlGenerator,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->cmsItemLoader = $cmsItemLoader;
        $this->urlGenerator = $urlGenerator;
        $this->errorHandler = $errorHandler;
    }

    /**
     * The method returns the full path of provided item.
     *
     * It accepts three kinds of references to the item:
     *
     * 1) URI with value_type://value format, e.g. type://42
     * 2) ID and value type as an array, e.g. [42, 'type']
     * 3) \Netgen\Layouts\Item\CmsItemInterface object
     *
     * @param mixed $value
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If provided item or item reference is not valid
     */
    public function getItemPath($value, string $type = UrlGeneratorInterface::TYPE_DEFAULT): string
    {
        try {
            $item = null;

            if (is_string($value)) {
                $itemUri = parse_url($value);
                if (!is_array($itemUri) || ($itemUri['scheme'] ?? '') === '' || !isset($itemUri['host'])) {
                    throw ItemException::invalidValue($value);
                }

                $item = $this->cmsItemLoader->load(
                    $itemUri['host'],
                    str_replace('-', '_', $itemUri['scheme'] ?? ''),
                );
            } elseif (is_array($value) && count($value) === 2) {
                $item = $this->cmsItemLoader->load($value[0], $value[1]);
            } elseif ($value instanceof CmsItemInterface) {
                $item = $value;
            }

            if (!$item instanceof CmsItemInterface) {
                throw ItemException::canNotLoadItem();
            }

            return $this->urlGenerator->generate($item, $type);
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        return '';
    }
}
