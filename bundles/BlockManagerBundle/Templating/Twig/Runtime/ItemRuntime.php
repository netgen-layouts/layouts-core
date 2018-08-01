<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\Exception\Item\ItemException;
use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\Item\CmsItemLoaderInterface;
use Netgen\BlockManager\Item\UrlGeneratorInterface;
use Throwable;

final class ItemRuntime
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemLoaderInterface
     */
    private $cmsItemLoader;

    /**
     * @var \Netgen\BlockManager\Item\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Netgen\BlockManager\Error\ErrorHandlerInterface
     */
    private $errorHandler;

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
     * 2) ID and value type as separate arguments
     * 3) \Netgen\BlockManager\Item\CmsItemInterface object
     *
     * @param mixed $value
     * @param string|null $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If provided item or item reference is not valid
     *
     * @return string
     */
    public function getItemPath($value, ?string $valueType = null): string
    {
        try {
            $item = null;

            if (is_string($value) && $valueType === null) {
                $itemUri = parse_url($value);
                if (!is_array($itemUri) || empty($itemUri['scheme']) || !isset($itemUri['host'])) {
                    throw ItemException::invalidValue($value);
                }

                $item = $this->cmsItemLoader->load(
                    $itemUri['host'],
                    str_replace('-', '_', $itemUri['scheme'])
                );
            } elseif ((is_int($value) || is_string($value)) && is_string($valueType)) {
                $item = $this->cmsItemLoader->load($value, $valueType);
            } elseif ($value instanceof CmsItemInterface) {
                $item = $value;
            }

            if (!$item instanceof CmsItemInterface) {
                throw ItemException::canNotLoadItem();
            }

            return $this->urlGenerator->generate($item);
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        return '';
    }
}
