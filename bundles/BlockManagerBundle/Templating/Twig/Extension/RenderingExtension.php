<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use Netgen\BlockManager\API\Values\Page\Zone;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
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
    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    protected $layoutService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

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
     * @var string
     */
    protected $blockController;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param string $blockController
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        LayoutService $layoutService,
        GlobalVariable $globalVariable,
        RendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        $blockController,
        LoggerInterface $logger = null
    ) {
        $this->layoutService = $layoutService;
        $this->globalVariable = $globalVariable;
        $this->viewRenderer = $viewRenderer;
        $this->fragmentHandler = $fragmentHandler;
        $this->blockController = $blockController;
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
                'ngbm_render_layout',
                array($this, 'renderValueObject'),
                array(
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_rule',
                array($this, 'renderValueObject'),
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
            new Twig_SimpleFunction(
                'ngbm_render_value_object',
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
            'ngbm' => $this->globalVariable,
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
    public function renderBlock(Block $block, array $parameters = array(), $context = ViewInterface::CONTEXT_DEFAULT)
    {
        try {
            if ($this->isBlockCacheable($block)) {
                return $this->fragmentHandler->render(
                    new ControllerReference(
                        $this->blockController,
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
     * Renders the provided Twig block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $context
     * @param string $twigBlockName
     * @param \Twig_Template $twigTemplate
     * @param string $twigContext
     * @param array $twigBlocks
     *
     * @throws \Exception If an error occurred
     *
     * @return string
     */
    protected function renderTwigBlock(Block $block, $context, $twigBlockName, Twig_Template $twigTemplate, $twigContext, array $twigBlocks = array())
    {
        try {
            ob_start();

            $twigTemplate->displayBlock($twigBlockName, $twigContext, $twigBlocks);

            $params = array('twig_block_content' => ob_get_clean());

            return $this->renderBlock($block, $params, $context);
        } catch (Exception $e) {
            ob_end_clean();

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
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     * @param string $viewType
     * @param array $parameters
     * @param string $context
     *
     * @throws \Exception If an error occurred
     *
     * @return string
     */
    public function renderItem(ItemInterface $item, $viewType, array $parameters = array(), $context = ViewInterface::CONTEXT_DEFAULT)
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
    public function renderValueObject($valueObject, array $parameters = array(), $context = ViewInterface::CONTEXT_DEFAULT)
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
     * @param array $twigBlocks
     *
     * @throws \Exception If an error occurred
     */
    public function displayZone(Zone $zone, $context, Twig_Template $twigTemplate, $twigContext, array $twigBlocks = array())
    {
        foreach ($zone as $block) {
            $blockDefinitionHandler = $block->getBlockDefinition()->getHandler();
            if ($blockDefinitionHandler instanceof TwigBlockDefinitionHandlerInterface) {
                echo $this->renderTwigBlock(
                    $block,
                    $context,
                    $blockDefinitionHandler->getTwigBlockName($block),
                    $twigTemplate,
                    $twigContext,
                    $twigBlocks
                );

                continue;
            }

            echo $this->renderBlock($block, array(), $context);
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
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     * @param \Exception $exception
     */
    protected function logItemError(ItemInterface $item, Exception $exception)
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
