<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Extension;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
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
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    protected $globalVariable;

    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface
     */
    protected $cacheableResolver;

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
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable $globalVariable
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface $cacheableResolver
     * @param \Symfony\Component\HttpKernel\Fragment\FragmentHandler $fragmentHandler
     * @param string $blockController
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        BlockService $blockService,
        GlobalVariable $globalVariable,
        ViewBuilderInterface $viewBuilder,
        RendererInterface $viewRenderer,
        CacheableResolverInterface $cacheableResolver,
        FragmentHandler $fragmentHandler,
        $blockController,
        LoggerInterface $logger = null
    ) {
        $this->blockService = $blockService;
        $this->globalVariable = $globalVariable;
        $this->viewBuilder = $viewBuilder;
        $this->viewRenderer = $viewRenderer;
        $this->cacheableResolver = $cacheableResolver;
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
                'ngbm_render_block',
                array($this, 'renderBlock'),
                array(
                    'needs_context' => true,
                    'is_safe' => array('html'),
                )
            ),
            new Twig_SimpleFunction(
                'ngbm_render_placeholder',
                array($this, 'renderPlaceholder'),
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
        return array(new RenderZone());
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
        try {
            return $this->viewRenderer->renderValueObject(
                $valueObject,
                $this->getViewContext($context, $viewContext),
                $parameters
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
     * Displays the provided zone.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Zone $zone
     * @param string $viewContext
     * @param \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate $twigTemplate
     *
     * @throws \Exception If an error occurred
     */
    public function displayZone(Zone $zone, $viewContext, ContextualizedTwigTemplate $twigTemplate)
    {
        $blocks = $this->blockService->loadZoneBlocks($zone);
        foreach ($blocks as $block) {
            echo $this->renderBlock(
                array(
                    'twig_template' => $twigTemplate,
                    'view_context' => $viewContext,
                ),
                $block
            );
        }
    }

    /**
     * Renders the provided block.
     *
     * @param array $context
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param array $parameters
     * @param string $viewContext
     *
     * @return string
     */
    public function renderBlock(array $context, Block $block, array $parameters = array(), $viewContext = null)
    {
        $viewContext = $this->getViewContext($context, $viewContext);

        try {
            /** @var \Netgen\BlockManager\View\View\BlockViewInterface $blockView */
            $blockView = $this->viewBuilder->buildView(
                $block,
                $viewContext,
                array(
                    'twig_template' => $this->getTwigTemplate($context),
                ) + $parameters
            );

            if (!$this->cacheableResolver->isCacheable($block) || !$blockView->isCacheable()) {
                return $this->viewRenderer->renderView($blockView);
            }

            return $this->fragmentHandler->render(
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
        } catch (Exception $e) {
            $errorMessage = sprintf('Error rendering a block with ID "%s"', $block->getId());

            return $this->handleException($e, $errorMessage);
        }
    }

    /**
     * Renders the provided placeholder.
     *
     * @param array $context
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $placeholder
     * @param array $parameters
     * @param string $viewContext
     *
     * @return string
     */
    public function renderPlaceholder(array $context, Block $block, $placeholder, array $parameters = array(), $viewContext = null)
    {
        try {
            return $this->viewRenderer->renderValueObject(
                $block->getPlaceholder($placeholder),
                $this->getViewContext($context, $viewContext),
                array(
                    'block' => $block,
                    'twig_template' => $this->getTwigTemplate($context),
                ) + $parameters
            );
        } catch (Exception $e) {
            $errorMessage = sprintf(
                'Error rendering a placeholder "%s" in block with ID "%s"',
                $placeholder,
                $block->getId()
            );

            return $this->handleException($e, $errorMessage);
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
     * Returns the correct view context based on provided Twig context and view context
     * provided through function call.
     *
     * @param array $context
     * @param string $viewContext
     *
     * @return string
     */
    protected function getViewContext(array $context, $viewContext = null)
    {
        if ($viewContext !== null) {
            return $viewContext;
        }

        if (!empty($context['view_context'])) {
            return $context['view_context'];
        }

        return ViewInterface::CONTEXT_DEFAULT;
    }

    /**
     * Returns the Twig template if it exists in provided Twig context.
     *
     * @param array $context
     *
     * @return \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate
     */
    protected function getTwigTemplate(array $context)
    {
        if (!isset($context['twig_template'])) {
            return null;
        }

        return $context['twig_template'];
    }
}
