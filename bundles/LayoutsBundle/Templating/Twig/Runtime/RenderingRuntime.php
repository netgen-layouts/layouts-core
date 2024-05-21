<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime;

use Generator;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Collection\Result\ManualItem;
use Netgen\Layouts\Collection\Result\Result;
use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\Locale\LocaleProviderInterface;
use Netgen\Layouts\View\RendererInterface;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;
use Twig\Environment;
use Twig\Template;
use Twig\TemplateWrapper;

use function get_debug_type;
use function is_object;
use function is_scalar;
use function iterator_to_array;
use function method_exists;
use function sprintf;

final class RenderingRuntime
{
    private BlockService $blockService;

    private RendererInterface $renderer;

    private LocaleProviderInterface $localeProvider;

    private RequestStack $requestStack;

    private ErrorHandlerInterface $errorHandler;

    private Environment $simpleTwig;

    public function __construct(
        BlockService $blockService,
        RendererInterface $renderer,
        LocaleProviderInterface $localeProvider,
        RequestStack $requestStack,
        ErrorHandlerInterface $errorHandler,
        Environment $simpleTwig
    ) {
        $this->blockService = $blockService;
        $this->renderer = $renderer;
        $this->localeProvider = $localeProvider;
        $this->requestStack = $requestStack;
        $this->errorHandler = $errorHandler;
        $this->simpleTwig = $simpleTwig;
    }

    /**
     * Renders the provided item.
     *
     * @param array<string, mixed> $context
     * @param array<string, mixed> $parameters
     */
    public function renderItem(array $context, CmsItemInterface $item, string $viewType, array $parameters = [], ?string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $item,
                $this->getViewContext($context, $viewContext),
                ['view_type' => $viewType] + $parameters,
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an item with value "%s" and value type "%s"',
                $item->getValue(),
                $item->getValueType(),
            );

            $this->errorHandler->handleError($t, $message);
        }

        return '';
    }

    /**
     * Renders the provided result.
     *
     * @param array<string, mixed> $context
     * @param array<string, mixed> $parameters
     */
    public function renderResult(array $context, Result $result, ?string $overrideViewType = null, ?string $fallbackViewType = null, array $parameters = [], ?string $viewContext = null): string
    {
        $item = $result->getItem();
        $slot = $result->getSlot();

        try {
            $viewType = $fallbackViewType;

            if ($overrideViewType !== null) {
                $viewType = $overrideViewType;
            } elseif ($item instanceof ManualItem && $item->getCollectionItem()->getViewType() !== null) {
                $viewType = $item->getCollectionItem()->getViewType();
            } elseif ($slot instanceof Slot && $slot->getViewType() !== null) {
                $viewType = $slot->getViewType();
            }

            if ($viewType === null) {
                throw new InvalidArgumentException(
                    'fallbackViewType',
                    'To render a result item, view type needs to be set through slot, or provided via "overrideViewType" or "fallbackViewType" parameters.',
                );
            }

            return $this->renderItem($context, $item, $viewType, $parameters, $viewContext);
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering an item with value "%s" and value type "%s"',
                $item->getValue(),
                $item->getValueType(),
            );

            $this->errorHandler->handleError($t, $message);
        }

        return '';
    }

    /**
     * Renders the provided value.
     *
     * @param array<string, mixed> $context
     * @param mixed $value
     * @param array<string, mixed> $parameters
     */
    public function renderValue(array $context, $value, array $parameters = [], ?string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $value,
                $this->getViewContext($context, $viewContext),
                $parameters,
            );
        } catch (Throwable $t) {
            $message = sprintf('Error rendering a value of type "%s"', get_debug_type($value));

            $this->errorHandler->handleError($t, $message, ['object' => $value]);
        }

        return '';
    }

    /**
     * Renders the provided zone.
     */
    public function renderZone(Layout $layout, string $zoneIdentifier, string $viewContext, ContextualizedTwigTemplate $twigTemplate): string
    {
        $locales = null;

        $request = $this->requestStack->getCurrentRequest();
        if ($request instanceof Request) {
            $locales = $this->localeProvider->getRequestLocales($request);
        }

        $zone = $layout->getZone($zoneIdentifier);
        $linkedZone = $zone->getLinkedZone();

        $blocks = $this->blockService->loadZoneBlocks(
            $linkedZone instanceof Zone ? $linkedZone : $zone,
            $locales,
        );

        return $this->renderValue(
            [],
            new ZoneReference($layout, $zoneIdentifier),
            [
                'blocks' => $blocks,
                'twig_template' => $twigTemplate,
            ],
            $viewContext,
        );
    }

    /**
     * Renders the provided block.
     *
     * @param array<string, mixed> $context
     * @param array<string, mixed> $parameters
     */
    public function renderBlock(array $context, Block $block, array $parameters = [], ?string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $block,
                $this->getViewContext($context, $viewContext),
                [
                    'twig_template' => $context['twig_template'] ?? null,
                ] + $parameters,
            );
        } catch (Throwable $t) {
            $message = sprintf('Error rendering a block with UUID "%s"', $block->getId()->toString());

            $this->errorHandler->handleError($t, $message);
        }

        return '';
    }

    /**
     * Renders the provided placeholder.
     *
     * @param array<string, mixed> $context
     * @param array<string, mixed> $parameters
     */
    public function renderPlaceholder(array $context, Block $block, string $placeholder, array $parameters = [], ?string $viewContext = null): string
    {
        try {
            return $this->renderer->renderValue(
                $block->getPlaceholder($placeholder),
                $this->getViewContext($context, $viewContext),
                [
                    'block' => $block,
                    'twig_template' => $context['twig_template'] ?? null,
                ] + $parameters,
            );
        } catch (Throwable $t) {
            $message = sprintf(
                'Error rendering a placeholder "%s" in block with UUID "%s"',
                $placeholder,
                $block->getId()->toString(),
            );

            $this->errorHandler->handleError($t, $message);
        }

        return '';
    }

    /**
     * Renders the provided template, with a reduced set of variables from the provided
     * parameters list. Variables included are only those which can be safely printed.
     *
     * @param array<string, mixed> $parameters
     */
    public function renderStringTemplate(string $string, array $parameters = []): string
    {
        try {
            $parameters = iterator_to_array($this->getTemplateVariables($parameters));
            $template = $this->simpleTwig->createTemplate($string);

            return $this->simpleTwig->resolveTemplate($template)->render($parameters);
        } catch (Throwable $t) {
            return '';
        }
    }

    /**
     * Returns the correct view context based on provided Twig context and view context
     * provided through function call.
     *
     * @param array<string, mixed> $context
     */
    private function getViewContext(array $context, ?string $viewContext = null): string
    {
        return $viewContext ?? $context['view_context'] ?? ViewInterface::CONTEXT_DEFAULT;
    }

    /**
     * Returns all safely printable variables: scalars and objects with __toString method.
     *
     * If the context has an instance of ContextualizedTwigTemplate, its context is also
     * included in the output. Any variables from the main context will override variables
     * from ContextualizedTwigTemplate objects.
     *
     * @param array<string, mixed> $parameters
     *
     * @return \Generator<string, mixed>
     */
    private function getTemplateVariables(array $parameters): Generator
    {
        foreach ($parameters as $name => $value) {
            if ($value instanceof ContextualizedTwigTemplate) {
                yield from $this->getTemplateVariables($value->getContext());
            }
        }

        foreach ($parameters as $name => $value) {
            if ($value instanceof Template || $value instanceof TemplateWrapper) {
                continue;
            }

            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                yield $name => $value;
            }
        }
    }
}
