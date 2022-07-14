<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\BlockView;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\TwigBlockDefinitionInterface;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use Netgen\Layouts\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function sprintf;

final class GetTwigBlockContentListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, 'block') => 'onRenderView'];
    }

    /**
     * Adds a parameter to the view with the Twig block content.
     */
    public function onRenderView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        $block = $view->getBlock();
        $blockDefinition = $block->getDefinition();

        if (!$blockDefinition instanceof TwigBlockDefinitionInterface) {
            return;
        }

        $twigContent = $this->getTwigBlockContent(
            $blockDefinition,
            $block,
            $view->getParameters(),
        );

        $event->addParameter('twig_content', $twigContent);
    }

    /**
     * Returns the Twig block content from the provided block.
     *
     * @param array<string, mixed> $parameters
     */
    private function getTwigBlockContent(
        TwigBlockDefinitionInterface $blockDefinition,
        Block $block,
        array $parameters
    ): string {
        if (!($parameters['twig_template'] ?? null) instanceof ContextualizedTwigTemplate) {
            return '';
        }

        foreach ($blockDefinition->getTwigBlockNames($block) as $blockName) {
            if ($parameters['twig_template']->hasBlock($blockName)) {
                return $parameters['twig_template']->renderBlock($blockName);
            }
        }

        return '';
    }
}
