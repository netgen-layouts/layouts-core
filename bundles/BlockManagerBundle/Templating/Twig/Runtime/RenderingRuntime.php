<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\Item\CmsItemInterface;
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
     */
    public function renderItem(array $context, CmsItemInterface $item, string $viewType, array $parameters = [], string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $item,
                $this->getViewContext($context, $viewContext),
                ['view_type' => $viewType] + $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an item with value "%s" and value type "%s"',
                $item->getValue(),
                $item->getValueType()
            );

            $this->errorHandler->handleError($t, $message);
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
    public function renderValue(array $context, $value, array $parameters = [], string $viewContext = null): string
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

            $this->errorHandler->handleError($t, $message, ['object' => $value]);
        }

        return '';
    }

    /**
     * Displays the provided zone.
     */
    public function displayZone(Zone $zone, string $viewContext, ContextualizedTwigTemplate $twigTemplate): void
    {
        $locales = null;

        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $locales = $this->localeProvider->getRequestLocales($request);
        }

        foreach ($this->blockService->loadZoneBlocks($zone, $locales) as $block) {
            echo $this->renderBlock(
                [
                    'twig_template' => $twigTemplate,
                    'view_context' => $viewContext,
                ],
                $block
            );
        }
    }

    /**
     * Renders the provided block.
     */
    public function renderBlock(array $context, Block $block, array $parameters = [], string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $block,
                $this->getViewContext($context, $viewContext),
                [
                    'twig_template' => $context['twig_template'] ?? null,
                ] + $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf('Error rendering a block with ID "%s"', $block->getId());

            $this->errorHandler->handleError($t, $message);
        }

        return '';
    }

    /**
     * Renders the provided placeholder.
     */
    public function renderPlaceholder(array $context, Block $block, string $placeholder, array $parameters = [], string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $block->getPlaceholder($placeholder),
                $this->getViewContext($context, $viewContext),
                [
                    'block' => $block,
                    'twig_template' => $context['twig_template'] ?? null,
                ] + $parameters
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering a placeholder "%s" in block with ID "%s"',
                $placeholder,
                $block->getId()
            );

            $this->errorHandler->handleError($t, $message);
        }

        return '';
    }

    /**
     * Returns the correct view context based on provided Twig context and view context
     * provided through function call.
     */
    private function getViewContext(array $context, string $viewContext = null): string
    {
        if ($viewContext !== null) {
            return $viewContext;
        }

        if (!empty($context['view_context'])) {
            return $context['view_context'];
        }

        return ViewInterface::CONTEXT_DEFAULT;
    }
}
