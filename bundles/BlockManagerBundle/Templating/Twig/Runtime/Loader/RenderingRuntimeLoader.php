<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\Loader;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\RenderingRuntime;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twig_RuntimeLoaderInterface;

/**
 * Runtime loader for RenderingRuntime class.
 *
 * @deprecated Remove when support for Symfony 2.8 ends.
 */
class RenderingRuntimeLoader implements Twig_RuntimeLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $renderer;

    /**
     * @var \Psr\Log\NullLogger
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\View\RendererInterface $renderer
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        BlockService $blockService,
        RendererInterface $renderer,
        LoggerInterface $logger = null
    ) {
        $this->blockService = $blockService;
        $this->renderer = $renderer;
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
        if ($class !== RenderingRuntime::class) {
            return null;
        }

        return new RenderingRuntime(
            $this->blockService,
            $this->renderer,
            $this->logger
        );
    }
}
