<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Item\Item;
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

class RenderingExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    const BLOCK_CONTROLLER = 'ngbm_block:viewBlockById';

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

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
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalHelper $globalHelper
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        BlockDefinitionRegistryInterface $blockDefinitionRegistry,
        LayoutService $layoutService,
        GlobalHelper $globalHelper,
        RendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        LoggerInterface $logger = null
    ) {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
        $this->layoutService = $layoutService;
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
        return 'ngbm_render';
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
            new Twig_SimpleFunction(
                'ngbm_render_item',
                array($this, 'renderItem'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_rule_target',
                array($this, 'renderValueObject'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_rule_condition',
                array($this, 'renderValueObject'),
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
                            'parameters' => $parameters,
                            'context' => $context,
                        )
                    ),
                    'esi'
                );
            }

            return $this->viewRenderer->renderValueObject($block, $parameters, $context);
        } catch (Exception $e) {
            $this->logBlockError($block, $e);

            if ($this->debug) {
                throw $e;
            }

            return '';
        }
    }

    /**
     * Renders the provided item.
     *
     * @param \Netgen\BlockManager\Item\Item $item
     * @param string $viewType
     * @param array $parameters
     * @param string $context
     *
     * @throws \Exception If an error occurred
     *
     * @return string
     */
    public function renderItem(Item $item, $viewType, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        try {
            return $this->viewRenderer->renderValueObject(
                $item,
                array('viewType' => $viewType) + $parameters,
                $context
            );
        } catch (Exception $e) {
            $this->logItemError($item, $e);

            if ($this->debug) {
                throw $e;
            }

            return '';
        }
    }

    /**
     * Renders the provided value object.
     *
     * @param mixed $valueObject
     * @param array $parameters
     * @param string $context
     *
     * @throws \Exception If an error occurred
     *
     * @return string
     */
    public function renderValueObject($valueObject, array $parameters = array(), $context = ViewInterface::CONTEXT_VIEW)
    {
        try {
            return $this->viewRenderer->renderValueObject(
                $valueObject,
                $parameters,
                $context
            );
        } catch (Exception $e) {
            $this->logValueObjectError($valueObject, $e);

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
        $blocks = $zone->getBlocks();

        $linkedZone = $this->layoutService->findLinkedZone($zone);
        if ($linkedZone instanceof Zone) {
            $blocks = $linkedZone->getBlocks();
        }

        foreach ($blocks as $block) {
            $blockDefinition = $this->blockDefinitionRegistry->getBlockDefinition(
                $block->getDefinitionIdentifier()
            );

            $blockDefinitionHandler = $blockDefinition->getHandler();
            if ($blockDefinitionHandler instanceof TwigBlockDefinitionHandlerInterface) {
                $this->displayTwigBlock(
                    $blockDefinitionHandler,
                    $block,
                    $twigTemplate,
                    $twigContext,
                    $twigBocks
                );

                continue;
            }

            echo $this->renderBlock($block, array(), $context);
        }
    }

    /**
     * Displays the provided twig block.
     *
     * @param \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface $blockDefinitionHandler
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param \Twig_Template $twigTemplate
     * @param array $twigContext
     * @param array $twigBocks
     *
     * @throws \Exception If an error occurred
     */
    protected function displayTwigBlock(
        TwigBlockDefinitionHandlerInterface $blockDefinitionHandler,
        Block $block,
        Twig_Template $twigTemplate,
        $twigContext,
        array $twigBocks = array()
    ) {
        try {
            $twigTemplate->displayBlock(
                $blockDefinitionHandler->getTwigBlockName($block),
                $twigContext,
                $twigBocks
            );
        } catch (Exception $e) {
            $this->logBlockError($block, $e);

            if ($this->debug) {
                throw $e;
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
    protected function logBlockError(Block $block, Exception $exception)
    {
        $this->logger->error(
            sprintf(
                'Error rendering a block with ID %d: %s',
                $block->getId(),
                $exception->getMessage()
            )
        );
    }

    /**
     * In most cases when rendering a Twig template on frontend
     * we do not want rendering of the item to crash the page,
     * hence we log an error.
     *
     * @param \Netgen\BlockManager\Item\Item $item
     * @param \Exception $exception
     */
    protected function logItemError(Item $item, Exception $exception)
    {
        $this->logger->error(
            sprintf(
                'Error rendering an item with ID %d and type %s: %s',
                $item->getValueId(),
                $item->getValueType(),
                $exception->getMessage()
            )
        );
    }

    /**
     * In most cases when rendering a Twig template on frontend
     * we do not want rendering of the value object to crash the page,
     * hence we log an error.
     *
     * @param mixed $valueObject
     * @param \Exception $exception
     */
    protected function logValueObjectError($valueObject, Exception $exception)
    {
        $this->logger->error(
            sprintf(
                'Error rendering a value object of type %s: %s',
                is_object($valueObject) ?
                    get_class($valueObject) :
                    gettype($valueObject),
                $exception->getMessage()
            )
        );
    }
}
