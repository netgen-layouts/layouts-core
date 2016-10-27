<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\UrlBuilderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig_SimpleFunction;
use Twig_Extension;
use Exception;

class ItemExtension extends Twig_Extension
{
    /**
     * @var \Netgen\BlockManager\Item\ItemLoaderInterface
     */
    protected $itemLoader;

    /**
     * @var \Netgen\BlockManager\Item\UrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var \Psr\Log\NullLogger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemLoaderInterface $itemLoader
     * @param \Netgen\BlockManager\Item\UrlBuilderInterface $urlBuilder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ItemLoaderInterface $itemLoader,
        UrlBuilderInterface $urlBuilder,
        LoggerInterface $logger = null
    ) {
        $this->itemLoader = $itemLoader;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets if debug is enabled or not.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'ngbm_item_path',
                array($this, 'getItemPath'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * The method returns the full path of provided item.
     *
     * It accepts three kinds of references to the item:
     *
     * 1) URI with value_type://value_id format, e.g. type://42
     * 2) ID and value type as separate arguments
     * 3) \Netgen\BlockManager\Item\ItemInterface object
     *
     * @param int|string|\Netgen\BlockManager\Item\ItemInterface $valueId
     * @param string|null $valueType
     *
     * @throws \Netgen\BlockManager\Exception\InvalidItemException If provided item or item reference is not valid
     *
     * @return string
     */
    public function getItemPath($valueId, $valueType = null)
    {
        try {
            $item = null;

            if (is_string($valueId) && $valueType === null) {
                $itemUri = parse_url($valueId);
                if (!is_array($itemUri) || empty($itemUri['scheme']) || empty($itemUri['host'])) {
                    throw new InvalidItemException(
                        sprintf(
                            'Item "%s" is not valid.',
                            $valueId
                        )
                    );
                }

                $item = $this->itemLoader->load($itemUri['host'], $itemUri['scheme']);
            } elseif (is_scalar($valueId) && is_string($valueType)) {
                $item = $this->itemLoader->load($valueId, $valueType);
            } elseif ($valueId instanceof ItemInterface) {
                $item = $valueId;
            }

            if (!$item instanceof ItemInterface) {
                throw new InvalidItemException(
                    sprintf(
                        'Item could not be loaded.',
                        $valueId
                    )
                );
            }

            return $this->urlBuilder->getUrl($item);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            if ($this->debug) {
                throw $e;
            }

            return '';
        }
    }
}
