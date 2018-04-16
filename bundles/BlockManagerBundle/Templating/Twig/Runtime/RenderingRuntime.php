<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Exception;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\Locale\LocaleProviderInterface;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;

final class RenderingRuntime
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    private $renderer;

    /**
     * @var \Netgen\BlockManager\Locale\LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\BlockManager\Error\ErrorHandlerInterface
     */
    private $errorHandler;

    public function __construct(
        BlockService $blockService,
        RendererInterface $renderer,
        LocaleProviderInterface $localeProvider,
        RequestStack $requestStack,
        ErrorHandlerInterface $errorHandler
    ) {
        $this->blockService = $blockService;
        $this->renderer = $renderer;
        $this->localeProvider = $localeProvider;
        $this->requestStack = $requestStack;
        $this->errorHandler = $errorHandler;
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
        try {
            return $this->renderer->renderValue(
                $item,
                $this->getViewContext($context, $viewContext),
                array('view_type' => $viewType) + $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an item with value "%s" and value type "%s"',
                $item->getValue(),
                $item->getValueType()
            );

            $this->errorHandler->handleError($t, $message);
        } catch (Exception $e) {
            $message = sprintf(
                'Error rendering an item with value "%s" and value type "%s"',
                $item->getValue(),
                $item->getValueType()
            );

            $this->errorHandler->handleError($e, $message);
        }

        return '';
    }

    /**
     * Renders the provided value.
     *
     * @param array $context
     * @param mixed $value
     * @param array $parameters
     * @param string $viewContext
     *
     * @return string
     */
    public function renderValue(array $context, $value, array $parameters = array(), $viewContext = null)
    {
        try {
            return $this->renderer->renderValue(
                $value,
                $this->getViewContext($context, $viewContext),
                $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering a value of type "%s"',
                is_object($value) ? get_class($value) : gettype($value)
            );

            $this->errorHandler->handleError($t, $message, array('object' => $value));
        } catch (Exception $e) {
            $message = sprintf(
                'Error rendering a value of type "%s"',
                is_object($value) ? get_class($value) : gettype($value)
            );

            $this->errorHandler->handleError($e, $message, array('object' => $value));
        }

        return '';
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
        $locales = null;

        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $locales = $this->localeProvider->getRequestLocales($request);
        }

        foreach ($this->blockService->loadZoneBlocks($zone, $locales) as $block) {
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
        try {
            return $this->renderer->renderValue(
                $block,
                $this->getViewContext($context, $viewContext),
                array(
                    'twig_template' => $this->getTwigTemplate($context),
                ) + $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf('Error rendering a block with ID "%s"', $block->getId());

            $this->errorHandler->handleError($t, $message);
        } catch (Exception $e) {
            $message = sprintf('Error rendering a block with ID "%s"', $block->getId());

            $this->errorHandler->handleError($e, $message);
        }

        return '';
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
            return $this->renderer->renderValue(
                $block->getPlaceholder($placeholder),
                $this->getViewContext($context, $viewContext),
                array(
                    'block' => $block,
                    'twig_template' => $this->getTwigTemplate($context),
                ) + $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering a placeholder "%s" in block with ID "%s"',
                $placeholder,
                $block->getId()
            );

            $this->errorHandler->handleError($t, $message);
        } catch (Exception $e) {
            $message = sprintf(
                'Error rendering a placeholder "%s" in block with ID "%s"',
                $placeholder,
                $block->getId()
            );

            $this->errorHandler->handleError($e, $message);
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
    private function getViewContext(array $context, $viewContext = null)
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
     * @return \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate|null
     */
    private function getTwigTemplate(array $context)
    {
        if (!isset($context['twig_template'])) {
            return;
        }

        return $context['twig_template'];
    }
}
