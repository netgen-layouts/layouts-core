<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Twig;

use Throwable;
use Twig\Template;

use function ob_end_clean;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;

/**
 * Wrapper around a Twig template with a context included (all variables
 * available inside the template).
 */
final class ContextualizedTwigTemplate
{
    private Template $template;

    /**
     * @var array<string, mixed>
     */
    private array $context;

    /**
     * @var array<string, mixed>
     */
    private array $blocks;

    /**
     * @param array<string, mixed> $context
     * @param array<string, mixed> $blocks
     */
    public function __construct(Template $template, array $context = [], array $blocks = [])
    {
        $this->template = $template;
        $this->context = $context;
        $this->blocks = $blocks;
    }

    /**
     * Returns the context for this template.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Returns if the template has a block with provided name.
     */
    public function hasBlock(string $blockName): bool
    {
        return $this->template->hasBlock($blockName, $this->context, $this->blocks);
    }

    /**
     * Renders the provided block. If block does not exist, an empty string will be returned.
     *
     * @throws \Throwable
     */
    public function renderBlock(string $blockName): string
    {
        if (!$this->template->hasBlock($blockName, $this->context, $this->blocks)) {
            return '';
        }

        $level = ob_get_level();
        ob_start();

        try {
            $this->template->displayBlock($blockName, $this->context, $this->blocks);
        } catch (Throwable $t) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $t;
        }

        return (string) ob_get_clean();
    }
}
