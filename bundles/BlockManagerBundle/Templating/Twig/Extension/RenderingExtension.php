<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Exception;
use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderBlock;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\TokenParser\RenderZone;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFunction;

class RenderingExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
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
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param string $blockController
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        GlobalVariable $globalVariable,
        RendererInterface $viewRenderer,
        FragmentHandler $fragmentHandler,
        $blockController,
        LoggerInterface $logger = null
    ) {
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
        $this->debug = (bool) $debug;
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
                'ngbm_render_item',
                array($this, 'renderItem'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_layout',
                array($this, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_parameter',
                array($this, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_rule',
                array($this, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_rule_target',
                array($this, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_rule_condition',
                array($this, 'renderValueObject'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_value_object',
                array($this, 'renderValueObject'),
                array(
                    'needs_context' => true,
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
            new RenderBlock(),
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
     * Renders the provided item.
     *
     * @param array $context
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     * @param string $viewType
     * @param array $parameters
     * @param string $viewContext
     *
     * @return string
     */
    public function renderItem(array $context, ItemInterface $item, $viewType, array $parameters = array(), $viewContext = null)
    {
        return $this->renderValueObject(
            $context,
            $item,
            array('view_type' => $viewType) + $parameters,
            $viewContext
        );
    }

    /**
     * Renders the provided value object.
     *
     * @param array $context
     * @param mixed $valueObject
     * @param array $parameters
     * @param string $viewContext
     *
     * @return string
     */
    public function renderValueObject(array $context, $valueObject, array $parameters = array(), $viewContext = null)
    {
        if ($viewContext === null) {
            $viewContext = !empty($context['view_context']) ?
                $context['view_context'] :
                ViewInterface::CONTEXT_DEFAULT;
        }

        try {
            return $this->viewRenderer->renderValueObject(
                $valueObject,
                $parameters,
                $viewContext
            );
        } catch (Exception $e) {
            $errorMessage = sprintf(
                'Error rendering a value object of type "%s"',
                is_object($valueObject) ? get_class($valueObject) : gettype($valueObject)
            );

            return $this->handleException($e, $errorMessage);
        }
    }

    /**
     * Displays the provided block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param string $viewContext
     * @param \Netgen\BlockManager\Block\BlockDefinition\Twig\ContextualizedTwigTemplate $twigTemplate
     *
     * @throws \Exception If an error occurred
     */
    public function displayBlock(Block $block, $viewContext, ContextualizedTwigTemplate $twigTemplate)
    {
        try {
            if ($this->isBlockCacheable($block)) {
                echo $this->fragmentHandler->render(
                    new ControllerReference(
                        $this->blockController,
                        array(
                            'blockId' => $block->getId(),
                            'context' => $viewContext,
                            '_ngbm_status' => 'published',
                        )
                    ),
                    'esi'
                );

                return;
            }

            echo $this->viewRenderer->renderValueObject($block, array('twigTemplate' => $twigTemplate), $viewContext);
        } catch (Exception $e) {
            $errorMessage = sprintf('Error rendering a block with ID "%s"', $block->getId());

            echo $this->handleException($e, $errorMessage);
        }
    }

    /**
     * Handles the exception based on provided debug flag.
     *
     * @param \Exception $exception
     * @param string $errorMessage
     *
     * @todo Refactor out to separate service
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function handleException(Exception $exception, $errorMessage)
    {
        $this->logger->error($errorMessage . ': ' . $exception->getMessage());

        if ($this->debug) {
            throw $exception;
        }

        return '';
    }

    /**
     * Returns if the block instance is cacheable.
     *
     * @todo Refactor out to separate service
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return bool
     */
    protected function isBlockCacheable(Block $block)
    {
        return false;
    }
}
