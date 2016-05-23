<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\View\ViewInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFunction;
use Twig_Extension;
use Twig_Template;
use Exception;

class NetgenBlockManagerExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    const BLOCK_CONTROLLER = 'ngbm_block:viewBlockById';

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper
     */
    protected $globalHelper;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler
     */
    protected $fragmentHandler;

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
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GlobalHelper $globalHelper,
        RendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        LoggerInterface $logger = null
    ) {
        $this->globalHelper = $globalHelper;
        $this->viewRenderer = $viewRenderer;
        $this->fragmentHandler = $fragmentHandler;
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
        return 'netgen_block_manager';
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
                'ngbm_render_block',
                array($this, 'renderBlock'),
                array(
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return \Twig_TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return array(
            new RenderZone(),
        );
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'ngbm' => $this->globalHelper,
        );
    }

    /**
     * Renders the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     * @param string $context
     *
     * @throws \Exception If an error occurred
     *
     * @return string
     */
    public function renderBlock(Block $block, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        try {
            if ($this->isBlockCacheable($block)) {
                return $this->fragmentHandler->render(
                    new ControllerReference(
                        self::BLOCK_CONTROLLER,
                        array(
                            'blockId' => $block->getId(),
                            'context' => $context,
                            'parameters' => $parameters,
                        )
                    ),
                    'esi'
                );
            }

            return $this->viewRenderer->renderValueObject($block, $context, $parameters);
        } catch (Exception $e) {
            $this->logError($block, $e);

            if ($this->debug) {
                throw $e;
            }

            return '';
        }
    }

    /**
     * Displays the provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Zone $zone
     * @param string $context
     * @param \Twig_Template $twigTemplate
     * @param array $twigContext
     * @param array $twigBocks
     *
     * @throws \Exception If an error occurred
     */
    public function displayZone(Zone $zone, $context, Twig_Template $twigTemplate, $twigContext, array $twigBocks = array())
    {
        foreach ($zone->getBlocks() as $block) {
            if ($block->getDefinitionIdentifier() !== TwigBlockHandler::DEFINITION_IDENTIFIER) {
                echo $this->renderBlock($block, array(), $context);
                continue;
            }

            try {
                $twigTemplate->displayBlock($block->getParameter('block_name'), $twigContext, $twigBocks);
            } catch (Exception $e) {
                $this->logError($block, $e);

                if ($this->debug) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Returns if the block instance is cacheable.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return bool
     */
    protected function isBlockCacheable(Block $block)
    {
        return false;
    }

    /**
     * In most cases when rendering a Twig template on frontend
     * we do not want rendering of the block to crash the page,
     * hence we log an error.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Exception $exception
     */
    protected function logError(Block $block, Exception $exception)
    {
        $this->logger->error(
            sprintf('Error rendering a block with ID %d: %s', $block->getId(), $exception->getMessage()),
            array('ngbm')
        );
    }
}
