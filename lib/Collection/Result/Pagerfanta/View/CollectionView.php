<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Result\Pagerfanta\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Twig\Environment;

use function array_key_exists;
use function is_string;
use function sprintf;

final class CollectionView implements ViewInterface
{
    private Environment $twig;

    private string $template;

    public function __construct(Environment $twig, string $template)
    {
        $this->twig = $twig;
        $this->template = $template;
    }

    public function getName(): string
    {
        return 'nglayouts_collection';
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

        if (!($options['block'] ?? null) instanceof Block) {
            throw new InvalidArgumentException(
                'options',
                sprintf(
                    'To render the collection view, "block" option must be an instance of %s',
                    Block::class,
                ),
            );
        }

        if (!is_string($options['collection_identifier'] ?? null)) {
            throw new InvalidArgumentException(
                'options',
                'To render the collection view, "collection_identifier" option must be a string',
            );
        }

        return $this->twig->render(
            $pagerTemplate,
            [
                'pager' => $pagerfanta,
            ] + $options,
        );
    }
}
