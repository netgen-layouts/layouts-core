<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Result\Pagerfanta\View;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Twig\Environment;

final class CollectionView implements ViewInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $template;

    public function __construct(Environment $twig, string $template)
    {
        $this->twig = $twig;
        $this->template = $template;
    }

    public function getName(): string
    {
        return 'ngbm_collection';
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = []): string
    {
        $pagerTemplate = $this->template;
        if (array_key_exists('template', $options)) {
            if (is_string($options['template'])) {
                $pagerTemplate = $options['template'];
            }

            unset($options['template']);
        }

        if (!isset($options['block']) || !($options['block'] instanceof Block)) {
            throw new InvalidArgumentException(
                'options',
                sprintf(
                    'To render the collection view, "block" option must be an instance of %s',
                    Block::class
                )
            );
        }

        if (!isset($options['collection_identifier']) || !is_string($options['collection_identifier'])) {
            throw new InvalidArgumentException(
                'options',
                'To render the collection view, "collection_identifier" option must be a string'
            );
        }

        return $this->twig->render(
            $pagerTemplate,
            [
                'pager' => $pagerfanta,
            ] + $options
        );
    }
}
