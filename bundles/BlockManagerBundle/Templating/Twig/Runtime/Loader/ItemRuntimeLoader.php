<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader;

use Netgen\BlockManager\Item\ItemLoaderInterface;
use Netgen\BlockManager\Item\UrlBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\ItemRuntime;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig_RuntimeLoaderInterface;

/**
 * Runtime loader for ItemRuntime class.
 *
 * @deprecated Remove when support for Symfony 2.8 ends.
 */
class ItemRuntimeLoader implements Twig_RuntimeLoaderInterface
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
     * Creates the runtime implementation of a Twig element (filter/function/test).
     *
     * @param string $class A runtime class
     *
     * @return object|null The runtime instance or null if the loader does not know how to create the runtime for this class
     */
    public function load($class)
    {
        if ($class !== ItemRuntime::class) {
            return null;
        }

        return new ItemRuntime(
            $this->itemLoader,
            $this->urlBuilder,
            $this->logger
        );
    }
}
